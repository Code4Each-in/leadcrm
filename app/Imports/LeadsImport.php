<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

/**
 * Reads an uploaded Lead CSV into rows keyed by column header (the
 * Lead field keys from LeadCsvFields - see its class docblock for why
 * plain snake_case headers round-trip unchanged through the default
 * heading-row formatter). Does no validation or creation itself -
 * LeadCsvController pulls the parsed rows back out via
 * Excel::toCollection() and validates/creates them against the same
 * LeadValidationRules a manual submission uses.
 */
class LeadsImport implements ToCollection, WithCustomCsvSettings, WithCustomValueBinder, WithHeadingRow
{
    private StringValueBinder $valueBinder;

    public function __construct()
    {
        $this->valueBinder = new StringValueBinder();
    }

    public function collection(Collection $rows): void
    {
        // Intentionally empty - see class docblock.
    }

    /**
     * Forces the delimiter to a comma rather than letting
     * PhpSpreadsheet auto-detect it from the file content. Without
     * this, a CSV with few or no commas (e.g. company/address values
     * that happen not to need one, or - worse - a file with only one
     * populated column) can have its delimiter mis-detected as a
     * space, silently truncating every value at its first space
     * ("Acme Trading Ltd" -> "Acme").
     */
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
        ];
    }

    /**
     * Forces every CSV cell to be read as a plain string rather than
     * letting PhpSpreadsheet's default value binder auto-detect
     * numeric-looking cells and cast them to PHP int/float. Without
     * this, a purely-numeric Company Number like "08192897" is
     * silently corrupted to 8192897 (leading zero dropped) and, on
     * top of that, fails LeadValidationRules' string rule outright -
     * this makes CSV cells behave exactly like manual form fields,
     * which are always strings (HTTP request input), so the two
     * flows validate identically.
     */
    public function bindValue(Cell $cell, mixed $value): bool
    {
        return $this->valueBinder->bindValue($cell, $value);
    }
}
