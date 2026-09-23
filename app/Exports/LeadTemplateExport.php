<?php

namespace App\Exports;

use App\Models\Product;
use App\Support\LeadCsvFields;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * A header-only CSV template for the given product - no rows, just
 * the columns LeadCsvController::import() expects back, in the same
 * order LeadCsvFields::forProduct() defines.
 */
class LeadTemplateExport implements FromArray, WithHeadings
{
    use Exportable;

    public function __construct(private readonly Product $product)
    {
    }

    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return LeadCsvFields::forProduct($this->product);
    }
}
