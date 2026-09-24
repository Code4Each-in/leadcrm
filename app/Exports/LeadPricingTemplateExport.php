<?php

namespace App\Exports;

use App\Support\LeadPricingCsvFields;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * A header-only CSV template for Pricing - no rows, just the columns
 * LeadPricingCsvController::import() expects back, in the same order
 * LeadPricingCsvFields::fields() defines. Mirrors
 * App\Exports\LeadTemplateExport.
 */
class LeadPricingTemplateExport implements FromArray, WithHeadings
{
    use Exportable;

    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return LeadPricingCsvFields::fields();
    }
}
