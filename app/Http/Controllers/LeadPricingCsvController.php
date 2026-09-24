<?php

namespace App\Http\Controllers;

use App\Exports\LeadPricingTemplateExport;
use App\Imports\LeadPricingsImport;
use App\Models\Lead;
use App\Models\LeadPricing;
use App\Models\Supplier;
use App\Services\LeadPricingCreationService;
use App\Support\LeadPricingCsvFields;
use App\Support\LeadPricingValidationRules;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel as ExcelType;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

/**
 * CSV template download / bulk import for Pricing - the Pricing
 * counterpart to LeadCsvController. Unlike Lead import (one file =
 * one product, chosen up front), a Pricing CSV can target a different
 * Lead on every row via its own "lead_id" column, so there's no
 * route-bound Product/Lead here - each row resolves and is validated
 * independently, with the exact same LeadPricingValidationRules a
 * manual Add Pricing submission uses, and created via the same
 * LeadPricingCreationService - so an imported row can never end up as
 * Pricing that would have failed if entered by hand, and behaves
 * identically to Pricing added manually.
 */
class LeadPricingCsvController extends Controller
{
    use AuthorizesRequests;

    private const MAX_ROWS = 1000;

    public function template()
    {
        $this->authorize('create', LeadPricing::class);

        return Excel::download(new LeadPricingTemplateExport(), 'pricing-import-template.csv', ExcelType::CSV);
    }

    public function showImport()
    {
        $this->authorize('create', LeadPricing::class);

        return view('leads.pricing-import', [
            'fields' => LeadPricingCsvFields::fields(),
            'hints' => LeadPricingCsvFields::hints(),
        ]);
    }

    public function import(Request $request)
    {
        $this->authorize('create', LeadPricing::class);

        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ], [
            'csv_file.required' => 'Please choose a CSV file to upload.',
            'csv_file.mimes' => 'The file must be a CSV file.',
            'csv_file.max' => 'The CSV file may not be larger than 10MB.',
        ]);

        try {
            $sheets = Excel::toCollection(new LeadPricingsImport(), $request->file('csv_file'));
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

        $rules = LeadPricingValidationRules::rules();
        $messages = LeadPricingValidationRules::messages();

        $errors = [];
        $validRows = [];

        foreach ($rows as $index => $row) {

            $data = collect($row->toArray())
                ->map(fn ($value) => is_string($value) ? trim($value) : $value)
                ->map(fn ($value) => $value === '' ? null : $value)
                ->all();

            // The row number as it appears in the actual CSV file -
            // row 1 is the header, so the first data row (index 0) is
            // row 2.
            $rowNumber = $index + 2;

            // Resolve lead_id (the business-facing id, e.g. "1500" or
            // "1500-1" - not the internal numeric primary key, same
            // precedence as Lead::resolveRouteBinding()) to a real,
            // non-soft-deleted, AU Savers Lead before running the
            // shared field rules below - mirrors how LeadCsvController
            // resolves the selected product into product_id ahead of
            // validation. Not part of LeadPricingValidationRules
            // itself since a manual submission never needs this step
            // (its Lead is already resolved via route-model-binding).
            $lead = filled($data['lead_id'] ?? null)
                ? Lead::where('lead_id', $data['lead_id'])->first() ?? Lead::where('id', $data['lead_id'])->first()
                : null;

            if (!$lead) {
                $errors[] = [
                    'row' => $rowNumber,
                    'field' => 'lead_id',
                    'message' => 'No lead was found with this Lead ID.',
                ];

                continue;
            }

            if (!$lead->isAuSavers()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'field' => 'lead_id',
                    'message' => 'Pricing is only applicable to AU Savers leads.',
                ];

                continue;
            }

            // Resolve supplier (name, e.g. "Octopus Energy" - not its
            // numeric id) to supplier_id before running the shared
            // field rules, same reasoning as lead_id above. Matching
            // is case-insensitive so a minor casing mismatch in the
            // spreadsheet doesn't fail the whole row.
            $supplier = filled($data['supplier'] ?? null)
                ? Supplier::whereRaw('LOWER(name) = ?', [strtolower(trim($data['supplier']))])->first()
                : null;

            if (!$supplier) {
                $errors[] = [
                    'row' => $rowNumber,
                    'field' => 'supplier',
                    'message' => 'No supplier was found with this name.',
                ];

                continue;
            }

            $data['supplier_id'] = $supplier->id;
            unset($data['supplier'], $data['lead_id']);

            $data['status'] = $data['status'] ?? 'draft';

            $validator = Validator::make($data, $rules, $messages);

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

            $validRows[] = ['lead' => $lead, 'data' => $validated];
        }

        if (!empty($errors)) {
            return back()
                ->withInput()
                ->with('importErrors', $errors)
                ->with('importTotalRows', $totalRows);
        }

        $creationService = app(LeadPricingCreationService::class);

        DB::transaction(function () use ($validRows, $creationService) {
            foreach ($validRows as $row) {
                $creationService->create($row['data'], $row['lead']);
            }
        });

        return redirect()
            ->route('leads.index')
            ->with(
                'success',
                count($validRows) === 1
                    ? '1 pricing record imported successfully.'
                    : count($validRows) . ' pricing records imported successfully.'
            );
    }
}
