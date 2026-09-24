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
 * Reads an uploaded Pricing CSV into rows keyed by column header (see
 * LeadPricingCsvFields). Does no validation or creation itself -
 * LeadPricingCsvController pulls the parsed rows back out via
 * Excel::toCollection() and validates/creates them against the same
 * LeadPricingValidationRules a manual Add Pricing submission uses.
 * Mirrors App\Imports\LeadsImport - same delimiter/value-binder fixes
 * apply here for the same reasons (see that class's docblocks).
 */
class LeadPricingsImport implements ToCollection, WithCustomCsvSettings, WithCustomValueBinder, WithHeadingRow
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

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
        ];
    }

    public function bindValue(Cell $cell, mixed $value): bool
    {
        return $this->valueBinder->bindValue($cell, $value);
    }
}
