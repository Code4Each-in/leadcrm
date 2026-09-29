<?php

namespace App\Exports;

use App\Support\MultisiteSitesCsv;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Header-only template for the Multiple Site sites CSV uploaded on
 * Add Lead / Edit - one data row per site.
 */
class MultisiteSitesTemplateExport implements FromArray, WithHeadings
{
    use Exportable;

    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return MultisiteSitesCsv::COLUMNS;
    }
}
