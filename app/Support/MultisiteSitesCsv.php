<?php

namespace App\Support;

use App\Imports\MultisiteSitesImport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

/**
 * Per-site data for a "Multiple Site" lead - one entry per site, each
 * with its own MPAN:
 *
 *   [['supply_address' => ?, 'mpan' => '...', 'mprn' => ?, 'spid' => ?], ...]
 *
 * Comes either from the sites CSV uploaded on Add Lead / Edit, or (Lead
 * CSV import) from a ","-separated list in a Multiple Site row's mpan
 * cell (";" is still accepted from older files). Both go through
 * validate(), so the count / format / duplicate rules are identical
 * wherever a Multiple Site batch is created. Entry n becomes site lead
 * "{base}-n" (see LeadCreationService); a blank MPRN / SPID falls
 * back to the lead's own. Postcode is no longer collected - a Postcode
 * column in an older file is simply ignored.
 */
class MultisiteSitesCsv
{
    public const COLUMNS = ['Supply Address', 'MPAN', 'MPRN', 'SPID'];

    /**
     * Per-site fields, keyed as in the CSV header slug / Lead columns.
     */
    public const SITE_FIELDS = ['supply_address', 'mpan', 'mprn', 'spid'];

    public const OVERFLOW_MESSAGE =
        'This row has more values than there are columns. Wrap a Supply Address containing commas in double quotes.';

    /**
     * Reads and validates an uploaded sites CSV against the Multiple
     * Site count. Throws a ValidationException keyed on "sites_csv"
     * (the form's file input) listing every problem found.
     *
     * @return array<int,array{supply_address:?string,mpan:?string,mprn:?string,spid:?string}>
     */
    public static function fromUpload(UploadedFile $file, int $expectedCount, ?int $exceptLeadId = null, bool $allowTaken = false): array
    {
        try {
            $rows = Excel::toCollection(new MultisiteSitesImport(), $file)->first() ?? collect();
        } catch (Throwable $e) {
            throw ValidationException::withMessages([
                'sites_csv' => 'Could not read the sites CSV. Please make sure it is a valid CSV file.',
            ]);
        }

        // Drop fully-blank rows (spreadsheet padding) - keeping the
        // original keys so error messages still point at the right
        // line of the file (row 1 is the header, not counted).
        $rows = $rows->filter(fn ($row) => collect($row)->filter(fn ($value) => trim((string) $value) !== '')->isNotEmpty());

        $first = $rows->first();

        if ($first !== null && !collect($first)->has('mpan')) {
            throw ValidationException::withMessages([
                'sites_csv' => 'The sites CSV must have the columns: ' . implode(', ', self::COLUMNS) . '.',
            ]);
        }

        $sites = [];
        $rowNumbers = [];
        $rowErrors = [];

        foreach ($rows as $index => $row) {
            $row = collect($row);

            // Cells beyond the header come back under numeric keys -
            // almost always an unquoted Supply Address containing a
            // comma, which shifts MPAN / MPRN / SPID into the wrong
            // columns, so the row can't be trusted.
            if ($row->contains(fn ($value, $key) => is_int($key) && trim((string) $value) !== '')) {
                $rowErrors[] = 'Row ' . ($index + 2) . ': ' . self::OVERFLOW_MESSAGE;
            }

            $sites[] = self::normaliseSite($row->all());
            $rowNumbers[] = $index + 2;
        }

        $errors = array_merge($rowErrors, self::validate(
            $sites,
            $expectedCount,
            fn (int $i) => 'Row ' . $rowNumbers[$i],
            $exceptLeadId,
            $allowTaken
        ));

        if ($errors) {
            throw ValidationException::withMessages(['sites_csv' => $errors]);
        }

        return $sites;
    }

    /**
     * Lead CSV import: a Multiple Site row's mpan cell holding
     * "mpan1,mpan2,..." - one per site. The cell has to be quoted in
     * the file, since the CSV itself is comma-delimited (spreadsheet
     * apps do that on save). ";" - the original separator - is still
     * accepted, so older files keep importing.
     */
    public static function fromMpanList(?string $value): array
    {
        return collect(preg_split('/[,;]/', (string) $value))
            ->map(fn ($mpan) => trim($mpan))
            ->filter(fn ($mpan) => $mpan !== '')
            ->map(fn ($mpan) => self::normaliseSite(['mpan' => $mpan]))
            ->values()
            ->all();
    }

    /**
     * Every rule a Multiple Site batch's per-site data must pass.
     *
     * @param callable(int):string $label how to name entry $i in a
     *   message ("Row 3" for the CSV file, "MPAN #2" for an import cell)
     * @param bool $allowTaken the user confirmed MPANs already used by
     *   other leads (Add Lead) - only that check is skipped; the same
     *   MPAN twice in this batch is still refused
     * @return array<int,string> error messages (empty = valid)
     */
    public static function validate(array $sites, int $expectedCount, callable $label, ?int $exceptLeadId = null, bool $allowTaken = false): array
    {
        $errors = [];
        $found = count($sites);

        if ($found !== $expectedCount) {
            $errors[] = LeadValidationRules::SITES_COUNT_MISMATCH_MESSAGE . " (expected {$expectedCount}, found {$found}).";
        }

        $messages = LeadValidationRules::messages();

        foreach ($sites as $i => $site) {
            if (($site['mpan'] ?? null) === null) {
                $errors[] = $label($i) . ': MPAN is required.';
                continue;
            }

            $validator = Validator::make($site, [
                'supply_address' => ['nullable', 'string', 'max:2000'],
                'mpan' => ['digits:13'],
                'mprn' => ['nullable', 'digits_between:6,8'],
                'spid' => ['nullable', 'digits_between:8,10'],
            ], $messages);

            foreach ($validator->errors()->all() as $message) {
                $errors[] = $label($i) . ': ' . $message;
            }
        }

        $byMpan = [];

        foreach ($sites as $i => $site) {
            if (($site['mpan'] ?? null) !== null) {
                $byMpan[$site['mpan']][] = $label($i);
            }
        }

        foreach ($byMpan as $mpan => $labels) {
            if (count($labels) > 1) {
                $errors[] = LeadValidationRules::DUPLICATE_MPAN_MESSAGE . " MPAN {$mpan} appears more than once (" . implode(', ', $labels) . ').';
            }
        }

        $taken = $allowTaken ? [] : MpanRegistry::taken(array_keys($byMpan), $exceptLeadId);

        foreach ($taken as $mpan) {
            $errors[] = $byMpan[$mpan][0] . ": MPAN {$mpan} is already used by another lead.";
        }

        return $errors;
    }

    /**
     * @return array<int,string> every MPAN in a sites array
     */
    public static function mpans(?array $sites): array
    {
        return array_values(array_filter(array_map(fn ($site) => $site['mpan'] ?? null, $sites ?? [])));
    }

    private static function normaliseSite(array $row): array
    {
        $site = [];

        foreach (self::SITE_FIELDS as $key) {
            $value = trim((string) ($row[$key] ?? ''));
            $site[$key] = $value === '' ? null : $value;
        }

        return $site;
    }
}
