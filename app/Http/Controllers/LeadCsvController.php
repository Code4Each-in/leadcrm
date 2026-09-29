<?php

namespace App\Http\Controllers;

use App\Exports\LeadTemplateExport;
use App\Http\Controllers\Concerns\ScopesProducts;
use App\Imports\LeadsImport;
use App\Models\Lead;
use App\Models\Product;
use App\Models\User;
use App\Services\LeadCreationService;
use App\Services\LeadWorkflowService;
use App\Support\LeadCsvFields;
use App\Support\LeadValidationRules;
use App\Support\MultisiteSitesCsv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
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
 *
 * On top of the shared field rules, each row is checked for:
 * - MPAN: not already used by another lead, nor by an earlier row of
 *   the same file. A Multiple Site row lists one MPAN per site in its
 *   mpan cell, separated by "," (";" from older files still works) -
 *   validated with the same MultisiteSitesCsv rules as the sites CSV
 *   on Add Lead.
 * - User Name: if given, must be the exact full name or email address
 *   of exactly one active Account Manager with access to the product -
 *   never a partial match. They're stored as the lead's intended
 *   Account Manager and assigned once the lead is assignable (see
 *   LeadWorkflowService::assignIntendedAccountManager()).
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
                ->with('importError', 'The file could not be read. Please upload a valid CSV file.');
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
                ->with('importError', 'The CSV file has no data rows.');
        }

        if ($totalRows > self::MAX_ROWS) {
            return back()
                ->withInput()
                ->with('importError', 'The CSV file has ' . number_format($totalRows) . ' rows. The limit is ' . number_format(self::MAX_ROWS) . ' per import - please split it into smaller files.');
        }

        $rules = LeadValidationRules::rules();
        $messages = LeadValidationRules::importMessages();

        $eligibleAccountManagers = app(LeadWorkflowService::class)
            ->assignableAccountManagers(new Lead(['product_id' => $product->id]));

        // Loaded once for the whole file - resolveAccountManager()
        // matches "User Name" cells against it.
        $users = User::get();

        $errors = [];
        $validRows = [];

        // MPAN => the row it first appeared on, across the whole file.
        $seenMpans = [];

        foreach ($rows as $index => $row) {

            // The row number as it appears in the actual CSV file -
            // row 1 is the header, so the first data row (index 0) is
            // row 2.
            $rowNumber = $index + 2;

            // Cells beyond the header row come back under numeric keys.
            // Almost always an unquoted Multiple Site MPAN list spilling
            // into the next columns - which shifts every later value,
            // so the row's other errors would only mislead.
            $overflow = collect($row->toArray())
                ->filter(fn ($value, $key) => is_int($key) && trim((string) $value) !== '');

            if ($overflow->isNotEmpty()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'field' => 'mpan',
                    'message' => 'This row has more values than there are columns. Wrap a list of MPANs in double quotes, e.g. "1234567890123,1234567890124".',
                ];

                continue;
            }

            $data = collect($row->toArray())
                ->reject(fn ($value, $key) => is_int($key))
                ->map(fn ($value) => is_string($value) ? trim($value) : $value)
                ->map(fn ($value) => $value === '' ? null : $value)
                ->all();

            $data['product_id'] = $product->id;
            $data['status'] = $data['status'] ?? 'draft';

            $rowErrors = [];

            $userName = $data['user_name'] ?? null;
            unset($data['user_name']);

            $accountManager = null;

            if ($userName !== null) {
                [$accountManager, $userError] = $this->resolveAccountManager((string) $userName, $product, $users, $eligibleAccountManagers);

                if ($userError) {
                    $rowErrors[] = ['field' => 'user_name', 'message' => $userError];
                }
            }

            // A Multiple Site row's mpan cell is a ","-separated list,
            // one per site - checked separately below rather than by
            // the single-MPAN field rule.
            $isMultisite = ($data['number_of_sites'] ?? null) === 'Multiple Site';
            $sites = null;

            if ($isMultisite) {
                $sites = MultisiteSitesCsv::fromMpanList($data['mpan'] ?? null);
                unset($data['mpan']);
            }

            $validator = Validator::make($data, $rules, $messages);

            foreach ($validator->errors()->messages() as $field => $fieldMessages) {
                $rowErrors[] = ['field' => $field, 'message' => $fieldMessages[0]];
            }

            if ($isMultisite) {
                $sitesCount = filter_var($data['sites_count'] ?? null, FILTER_VALIDATE_INT);

                if ($sites) {
                    if ($sitesCount !== false) {
                        foreach (MultisiteSitesCsv::validate($sites, $sitesCount, fn (int $i) => 'MPAN #' . ($i + 1)) as $message) {
                            $rowErrors[] = ['field' => 'mpan', 'message' => $message];
                        }
                    }
                } elseif ($data['status'] === 'published') {
                    $rowErrors[] = [
                        'field' => 'mpan',
                        'message' => 'A published Multiple Site row needs one MPAN per site, separated by commas.',
                    ];
                }
            }

            // The same MPAN on two rows of this file.
            $rowMpans = $isMultisite ? MultisiteSitesCsv::mpans($sites) : array_filter([$data['mpan'] ?? null]);

            foreach (array_unique($rowMpans) as $mpan) {
                if (isset($seenMpans[$mpan])) {
                    $rowErrors[] = [
                        'field' => 'mpan',
                        'message' => "MPAN {$mpan} is already used on row {$seenMpans[$mpan]} of this file.",
                    ];
                } else {
                    $seenMpans[$mpan] = $rowNumber;
                }
            }

            if ($rowErrors) {
                foreach ($rowErrors as $error) {
                    $errors[] = ['row' => $rowNumber] + $error;
                }

                continue;
            }

            $validated = $validator->validated();
            $validated['created_by'] = Auth::id();
            $validated['intended_account_manager_id'] = $accountManager?->id;

            $validRows[] = ['data' => $validated, 'sites' => $sites ?: null];
        }

        if (!empty($errors)) {
            return back()
                ->withInput()
                ->with('importErrors', $errors)
                ->with('importTotalRows', $totalRows);
        }

        $creationService = app(LeadCreationService::class);

        $created = DB::transaction(function () use ($validRows, $creationService) {
            $created = collect();

            foreach ($validRows as $row) {
                $lead = $creationService->create($row['data'], $row['sites']);

                // A published Multiple Site row creates its whole batch.
                $created = $created->merge($lead->base_lead_id ? $lead->siblingSites()->get() : [$lead]);
            }

            return $created;
        });

        return redirect()
            ->route('leads.index')
            ->with('success', $this->importSummary(Lead::with('assignee:id,name')->findMany($created->pluck('id')), $product));
    }

    /**
     * e.g. "12 leads imported for AU Savers (10 published, 2 drafts).
     * 10 assigned to John Smith." - counting site leads individually,
     * and only the assignments that actually happened.
     */
    private function importSummary(Collection $leads, Product $product): string
    {
        $total = $leads->count();
        $drafts = $leads->where('status', 'draft')->count();
        $published = $total - $drafts;
        $noun = fn (int $n, string $word) => $n . ' ' . str($word)->plural($n);

        $summary = match (true) {
            $drafts === 0 => $noun($total, 'lead') . " imported and published for {$product->name}.",
            $published === 0 => $noun($total, 'lead') . ' imported as ' . ($total === 1 ? 'a draft' : 'drafts') . " for {$product->name}.",
            default => $noun($total, 'lead') . " imported for {$product->name} ({$published} published, " . $noun($drafts, 'draft') . ').',
        };

        $assigned = $leads->filter(fn (Lead $lead) => $lead->assignee !== null);

        if ($assigned->isNotEmpty()) {
            $names = $assigned->pluck('assignee.name')->unique();

            $summary .= ' ' . $assigned->count() . ' assigned to '
                . ($names->count() === 1 ? $names->first() : "{$names->count()} Account Managers") . '.';
        }

        return $summary;
    }

    /**
     * The "User Name" column: the exact full name or email address of
     * one Account Manager, who must be active and have access to this
     * product (the same list the manual Assign dropdown offers).
     * Case, surrounding spaces and repeated inner spaces are ignored;
     * anything else must match exactly - a partial name never assigns
     * a lead to someone by guesswork. A value that looks like an email
     * is matched on email only, anything else on name only.
     *
     * @param Collection<int,User> $users every user, loaded once per import
     * @return array{0: ?User, 1: ?string} the Account Manager, or an error
     */
    private function resolveAccountManager(string $value, Product $product, Collection $users, Collection $eligible): array
    {
        $normalise = fn (?string $text) => mb_strtolower(trim(preg_replace('/\s+/u', ' ', (string) $text)));

        $needle = $normalise($value);
        $byEmail = filter_var($needle, FILTER_VALIDATE_EMAIL) !== false;

        $matches = $users->filter(fn (User $user) => $normalise($byEmail ? $user->email : $user->name) === $needle);

        if ($matches->isEmpty()) {
            return [null, $byEmail
                ? "No user found with the email address '{$value}'."
                : "No user found with the name '{$value}'. Enter the Account Manager's exact full name or email address."];
        }

        $managers = $matches->filter(fn (User $user) => $user->isManager());

        if ($managers->isEmpty()) {
            return [null, "'{$value}' is not an Account Manager."];
        }

        if ($managers->count() > 1) {
            return [null, "More than one Account Manager is named '{$value}'. Please use their email address instead."];
        }

        $manager = $managers->first();

        if (!$eligible->contains('id', $manager->id)) {
            return [null, "'{$value}' is not an active Account Manager for {$product->name}."];
        }

        return [$manager, null];
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
