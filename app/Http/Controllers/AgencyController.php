<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AgencyController extends Controller
{
    // public function index()
    // {
    //     $selectedAgencyIds = session('agency_ids');

    //     if (!empty($selectedAgencyIds)) {
    //         $agencies = Agency::whereIn('id', $selectedAgencyIds)->latest()->get();
    //     } else {
    //         $agencies = Agency::latest()->get();
    //     }

    //     return view('agency.index', compact('agencies'));
    // }
    public function index(Request $request)
    {
        $authUser = Auth::user();
        $roleName = strtolower($authUser->role->name);

        $agenciesQuery = Agency::latest();

        // Example: role-based restriction if needed
        if ($roleName === 'admin') {
            // Only agencies assigned to this admin (if applicable)
            $agenciesQuery->where('agency_id', $authUser->agency_id);
        }

        // AJAX server-side processing
        if ($request->ajax()) {
            $baseQuery = clone $agenciesQuery;

            if (!empty($request->search['value'])) {
                $search = $request->search['value'];
                $agenciesQuery->where(function ($q) use ($search) {
                    $q->where('agency_name', 'like', "%{$search}%")
                    ->orWhere('primary_contact_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
                });
            }

            $total = $baseQuery->count();
            $filtered = $agenciesQuery->count();

            $agencies = $agenciesQuery->skip($request->start ?? 0)
                                    ->take($request->length ?? 10)
                                    ->get();

            $data = $agencies->map(function ($agency) {
                return [
                    'agency_name' => $agency->agency_name,
                    'primary_contact_name' => $agency->primary_contact_name,
                    'phone' => $agency->phone,
                    'address' => $agency->address . ', ' . $agency->city . ', ' . $agency->state . ' - ' . $agency->zip,
                    'id' => $agency->id
                ];
            });

            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => $total,
                "recordsFiltered" => $filtered,
                "data" => $data
            ]);
        }

        // Normal page load (non-AJAX)
        $agencies = $agenciesQuery->get(); // For pre-rendered modals
        $totalAgencies = $agenciesQuery->count();

        return view('agency.index', compact('agencies', 'authUser', 'totalAgencies'));
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agency_name'           => 'required',
            'primary_contact_name'  => 'required',
            'primary_email' => 'required|email|unique:agencies,primary_email',
            'password'              => 'required|min:6',
            'phone'                 => 'required',
            'address'               => 'required',
            'city'                  => 'required',
            'state'                 => 'required',
            'zip'                   => 'required',
            'logo'                  => 'nullable|mimes:jpg,jpeg,png,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // Upload logo
            $logoPath = null;

            if ($request->hasFile('logo')) {

                $file = $request->file('logo');

                $filename = time() . '_' . $file->getClientOriginalName();

                $destinationPath = public_path('assets/logos');

                // create folder if not exists
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }

                $file->move($destinationPath, $filename);

                $logoPath = 'assets/logos/' . $filename;
            }

            // Create agency
            $agency = Agency::create([
                'agency_name'          => $request->agency_name,
                'primary_contact_name' => $request->primary_contact_name,
                'primary_email'        => $request->primary_email,
                'phone'                => $request->phone,
                'address'              => $request->address,
                'city'                 => $request->city,
                'state'                => $request->state,
                'zip'                  => $request->zip,
                'logo'                 => $logoPath,
            ]);

            // Create user
            User::create([
                'role_id'   => 2,
                'name'      => $request->primary_contact_name,
                'email'     => $request->primary_email,
                'password'  => Hash::make($request->password),
                'address'   => $request->address,
                'city'      => $request->city,
                'state'     => $request->state,
                'zip'       => $request->zip,
                'agency_id' => $agency->id,
            ]);

            DB::commit();

            return response()->json(['success' => 'Agency + User created successfully']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $agency = Agency::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'agency_name'           => 'required',
            'primary_contact_name'  => 'required',
            'primary_email'         => 'required|email',
            'phone'                 => 'required',
            'address'               => 'required',
            'city'                  => 'required',
            'state'                 => 'required',
            'zip'                   => 'required',
            'logo'                  => 'nullable|mimes:jpg,jpeg,png,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // Upload new logo
            if ($request->hasFile('logo')) {

                // delete old file
                if ($agency->logo && file_exists(public_path($agency->logo))) {
                    unlink(public_path($agency->logo));
                }

                $filename = time() . '_' . $request->file('logo')->getClientOriginalName();
                $request->file('logo')->move(public_path('assets/images'), $filename);

                $agency->logo = 'assets/images/' . $filename;
            }

            $agency->update($request->except(['logo', 'password']));

            // Update user
            $user = User::where('agency_id', $agency->id)->first();
            if ($user) {
                $user->update([
                    'name'    => $request->primary_contact_name,
                    'email'   => $request->primary_email,
                    'address' => $request->address,
                    'city'    => $request->city,
                    'state'   => $request->state,
                    'zip'     => $request->zip,
                ]);

                if ($request->password) {
                    $user->password = Hash::make($request->password);
                    $user->save();
                }
            }

            DB::commit();

            return response()->json(['success' => 'Agency updated successfully']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        Agency::findOrFail($id)->delete();

        return response()->json(['success' => 'Agency deleted successfully']);
    }
    public function setAgency(Request $request)
    {
        session(['agency_ids' => $request->agency_ids ?? []]);

        return response()->json(['success' => true]);
    }
    public function showAgency()
    {
          $user = auth()->user();

        $agency = \App\Models\Agency::find($user->agency_id);

        $leadCount = 0; // your logic
        $teamCount = 0; // your logic

        return view('agency.show', compact('leadCount', 'teamCount', 'agency'));
    }
    public function detailUpdate(Request $request)
    {
        $agency = \App\Models\Agency::find(auth()->user()->agency_id);

        if (!$agency) {
            return back()->with('error', 'Agency not found');
        }


        $request->validate([
            'agency_name' => 'required',
            'primary_contact_name' => 'required',
            'primary_email' => 'required|email|unique:agencies,primary_email,' . $agency->id,
            'phone' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'logo' => 'nullable|mimes:jpg,jpeg,png,svg|max:2048',
        ]);


        if ($request->hasFile('logo')) {

            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('assets/logos');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $filename);

            $agency->logo = 'assets/logos/' . $filename;
        }


        $agency->update([
            'agency_name' => $request->agency_name,
            'primary_contact_name' => $request->primary_contact_name,
            'primary_email' => $request->primary_email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
        ]);

        return back()->with('success', 'Agency updated successfully');
    }
}
