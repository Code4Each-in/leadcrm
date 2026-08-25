<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLS;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LeadImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,csv'
        ]);

        $authUser = Auth::user();
        $authAgencyId = $authUser->agency_id;

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->getPathname();

        // Parse file
        $extension = strtolower($file->getClientOriginalExtension());
        $rows = [];

        if ($extension === 'xlsx') {
            $xlsx = \Shuchkin\SimpleXLSX::parse($filePath);
            if (!$xlsx) return back()->with('error', 'Failed to parse XLSX file.');
            $rows = $xlsx->rows();

        } elseif ($extension === 'xls') {
            $xls = \Shuchkin\SimpleXLS::parse($filePath);
            if (!$xls) return back()->with('error', 'Failed to parse XLS file.');
            $rows = $xls->rows();

        } elseif ($extension === 'csv') {
            if (($handle = fopen($filePath, 'r')) !== false) {
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $rows[] = $data;
                }
                fclose($handle);
            } else {
                return back()->with('error', 'Failed to parse CSV file.');
            }

        } else {
            return back()->with('error', 'Unsupported file format.');
        }

        if (count($rows) < 2) {
            return back()->with('error', 'File is empty.');
        }

        // Validate header
        $expectedHeader = ['name','phone','email','company','city','source','notes'];
        $header = array_map('strtolower', $rows[0]);

        if ($header !== $expectedHeader) {
            return back()->with('error', 'Invalid template.');
        }

        // Fetch AEs
        $accountExecs = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('users.status', 1)
            ->whereRaw('LOWER(roles.name) = ?', ['account executive'])
            ->where('users.agency_id', $authAgencyId)
            ->select('users.*')
            ->get();

        if ($accountExecs->isEmpty()) {
            return back()->with('error', 'No Account Executives found.');
        }

        $pointer = 0;
        $insertedCount = 0;
        $failedCount = 0;
        $failedRows = [];

        //  Track assignments for email batching
        $assignmentCounts = [];

        foreach ($rows as $index => $row) {

            if ($index === 0) continue;

            $name  = isset($row[0]) ? trim($row[0]) : '';
            $phone = isset($row[1]) ? trim($row[1]) : '';
            $email = isset($row[2]) ? trim($row[2]) : '';
            $reason = null;

            if (empty($name) || (empty($email) && empty($phone))) {
                $reason = "Missing fields (name: $name, email: $email, phone: $phone)";
            }

            if (!$reason) {
                $exists = DB::table('leads')
                    ->where(function($q) use ($email, $phone) {
                        if ($email) $q->orWhere('email', $email);
                        if ($phone) $q->orWhere('phone', $phone);
                    })
                    ->exists();

                if ($exists) {
                    $reason = 'Duplicate record.';
                }
            }

            if ($reason) {
                $failedCount++;
                $failedRows[] = [
                    'row' => $row,
                    'reason' => $reason,
                    'row_number' => $index + 1
                ];
                continue;
            }

            try {

                //  Assign AE BEFORE insert
                $assignedUser = $accountExecs[$pointer];
                $pointer = ($pointer + 1) % $accountExecs->count();

                //  Insert lead
                $leadId = DB::table('leads')->insertGetId([
                    'name'       => $name,
                    'phone'      => $phone,
                    'email'      => $email,
                    'company'    => $row[3] ?? null,
                    'city'       => $row[4] ?? null,
                    'source'     => $row[5] ?? null,
                    'status'     => 'In Progress',
                    'agency_id'  => $authAgencyId,
                    'created_by' => $authUser->id,
                    'assigned_to'=> $assignedUser->id,
                    'notes'      => $row[6] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                //  Track count for email
                $userId = $assignedUser->id;

                if (!isset($assignmentCounts[$userId])) {
                    $assignmentCounts[$userId] = 0;
                }

                $assignmentCounts[$userId]++;

                $insertedCount++;

            } catch (\Exception $e) {
                $failedCount++;
                $failedRows[] = [
                    'row' => $row,
                    'reason' => $e->getMessage(),
                    'row_number' => $index + 1
                ];
            }
        }

        //  Send ONE email per AE
        foreach ($assignmentCounts as $userId => $count) {

            $user = \App\Models\User::find($userId);

            if ($user) {
                $user->notify(new \App\Notifications\LeadStatusNotification(null, 'bulk_assign', $count));
            }
        }

        // Save failed CSV
        $failedFileName = null;

        if (!empty($failedRows)) {
            $failedFileName = 'failed_' . Str::random(6) . '_' . time() . '.csv';
            $handle = fopen(storage_path('app/' . $failedFileName), 'w');

            $csvHeader = array_merge($rows[0], ['reason', 'row_number']);
            fputcsv($handle, $csvHeader);

            foreach ($failedRows as $fail) {
                $rowData = $fail['row'];
                $rowData[] = $fail['reason'];
                $rowData[] = $fail['row_number'];
                fputcsv($handle, $rowData);
            }

            fclose($handle);
        }

        // Log upload
        DB::table('lead_upload_logs')->insert([
            'file_name'      => $fileName,
            'inserted_count' => $insertedCount,
            'failed_count'   => $failedCount,
            'failed_file'    => $failedFileName,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return back()->with([
            'import_result' => true,
            'success' => "Upload completed. Inserted: $insertedCount, Failed: $failedCount",
            'failed_rows' => $failedRows
        ]);
    }
}
