<?php

namespace App\Http\Controllers;

use App\Exports\LeadTemplateExport;
use App\Http\Controllers\Concerns\ScopesProducts;
use App\Imports\LeadsImport;
use App\Models\Product;
use App\Services\LeadCreationService;
use App\Support\LeadCsvFields;
use App\Support\LeadValidationRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel as ExcelType;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

/**
 * CSV template download / bulk import for Leads. A CSV row is
 * validated with the exact same LeadValidationRules a manual
 * create-lead submission uses, and created via the same
 * LeadCreationService - so a row can never end up as a Lead that
 * would have failed if entered by hand, and imported leads behave
 * identically to manually created ones (lead_id reservation,
 * Multiple Site batch expansion on publish, etc).
 */
class LeadCsvController extends Controller
{
    use ScopesProducts;

    private const MAX_ROWS = 1000;

    public function template(Product $product)
    {
        $this->authorizeProduct($product);

        $filename = 'lead-import-template-' . str($product->name)->slug() . '.csv';

        return Excel::download(new LeadTemplateExport($product), $filename, ExcelType::CSV);
    }

    public function showImport(Product $product)
    {
        $this->authorizeProduct($product);

        return view('leads.import', [
            'product' => $product,
            'fields' => LeadCsvFields::forProduct($product),
            'hints' => LeadCsvFields::hints(),
        ]);
    }

    public function import(Request $request, Product $product)
    {
        $this->authorizeProduct($product);

        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ], [
            'csv_file.required' => 'Please choose a CSV file to upload.',
            'csv_file.mimes' => 'The file must be a CSV file.',
            'csv_file.max' => 'The CSV file may not be larger than 10MB.',
        ]);

        try {
            $sheets = Excel::toCollection(new LeadsImport(), $request->file('csv_file'));
        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->with('importError', 'Could not read the uploaded file. Please make sure it is a valid CSV file.');
        }

        $rows = $sheets->first() ?? collect();

        // Drop fully-blank trailing rows (e.g. from a spreadsheet
        // editor padding the sheet) so they don't get reported as
        // validation failures for a row the user never meant to fill in.
        $rows = $rows->filter(fn ($row) => collect($row)->filter(fn ($value) => trim((string) $value) !== '')->isNotEmpty())
            ->values();

        $totalRows = $rows->count();

        if ($totalRows === 0) {
            return back()
                ->withInput()
                ->with('importError', 'The uploaded CSV file has no data rows.');
        }

        if ($totalRows > self::MAX_ROWS) {
            return back()
                ->withInput()
                ->with('importError', "The uploaded CSV file has {$totalRows} rows - the maximum per import is " . self::MAX_ROWS . '. Please split it into smaller files.');
        }

        $rules = LeadValidationRules::rules();
        $messages = LeadValidationRules::messages();

        $errors = [];
        $validRows = [];

        foreach ($rows as $index => $row) {

            $data = collect($row->toArray())
                ->map(fn ($value) => is_string($value) ? trim($value) : $value)
                ->map(fn ($value) => $value === '' ? null : $value)
                ->all();

            $data['product_id'] = $product->id;
            $data['status'] = $data['status'] ?? 'draft';

            $validator = Validator::make($data, $rules, $messages);

            // The row number as it appears in the actual CSV file -
            // row 1 is the header, so the first data row (index 0) is
            // row 2.
            $rowNumber = $index + 2;

            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $field => $fieldMessages) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'field' => $field,
                        'message' => $fieldMessages[0],
                    ];
                }

                continue;
            }

            $validated = $validator->validated();
            $validated['created_by'] = Auth::id();

            $validRows[] = $validated;
        }

        if (!empty($errors)) {
            return back()
                ->withInput()
                ->with('importErrors', $errors)
                ->with('importTotalRows', $totalRows);
        }

        $creationService = app(LeadCreationService::class);

        DB::transaction(function () use ($validRows, $creationService) {
            foreach ($validRows as $row) {
                $creationService->create($row);
            }
        });

        return redirect()
            ->route('leads.index')
            ->with(
                'success',
                count($validRows) === 1
                    ? "1 lead imported successfully for {$product->name}."
                    : count($validRows) . " leads imported successfully for {$product->name}."
            );
    }

    /**
     * Blocks a user from downloading a template or importing leads
     * for a product they're not assigned to - the same product
     * scoping already used to decide which products they see on the
     * manual create-lead form.
     */
    private function authorizeProduct(Product $product): void
    {
        $user = Auth::user();

        if (!$this->userCanUseProduct($user, $product)) {
            abort(403, 'You are not allowed to use this product.');
        }
    }
}
