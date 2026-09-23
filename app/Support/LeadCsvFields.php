<?php

namespace App\Support;

use App\Models\Product;

/**
 * The CSV column set for a given product's Lead template/import -
 * common Business/Contact Information fields every product shares,
 * plus that product's own panel fields, mirroring exactly what
 * resources/views/leads/create.blade.php shows/hides per product
 * (see its updateProductPanels() JS): products 1 (NFS) and 2 (AF4U)
 * share the funding panel, product 3 (AU Savers) gets the sites/
 * meter panel.
 *
 * Column headers are the raw Lead field keys (e.g.
 * "company_business_name") rather than prose labels - they round-trip
 * through Maatwebsite Excel's default heading-row formatter
 * unchanged (already-snake_case survives Str::slug($v, '_')), and
 * they match LeadValidationRules::rules() exactly, so an uploaded
 * row can be validated with the very same rules a manual submission
 * is, no header-label translation step needed.
 */
class LeadCsvFields
{
    /**
     * @return array<int,string> ordered field keys
     */
    public static function commonFields(): array
    {
        return [
            'company_type',
            'company_business_name',
            'company_number',
            'business_start_date',
            'business_type',
            'business_registered_address',
            'business_trading_address',
            'customer_name',
            'contact_person',
            'date_of_birth',
            'phone_no',
            'mobile_no',
            'email',
            'notes',
        ];
    }

    /**
     * NFS / AF4U specific fields (product ids 1 and 2).
     *
     * @return array<int,string>
     */
    public static function nfsAf4uFields(): array
    {
        return [
            'gross_sales',
            'funds_required',
            'funds_term_months',
            'home_owner',
            'vat_registered',
            'loan_purpose',
            'funds_usage_details',
        ];
    }

    /**
     * AU Savers specific fields (product id 3).
     *
     * @return array<int,string>
     */
    public static function auSaversFields(): array
    {
        return [
            'postcode',
            'supply_address',
            'number_of_sites',
            'sites_count',
            'mpan',
            'mprn',
            'spid',
        ];
    }

    /**
     * Field keys for the given product, in template column order -
     * common fields, then the product-specific panel, then status.
     *
     * @return array<int,string>
     */
    public static function forProduct(Product $product): array
    {
        $productFields = match (true) {
            in_array($product->id, [1, 2], true) => self::nfsAf4uFields(),
            $product->id === 3 => self::auSaversFields(),
            default => [],
        };

        return array_merge(self::commonFields(), $productFields, ['status']);
    }

    /**
     * One-line, human-readable note per field explaining allowed
     * values/format, used as a hint row under the template header
     * and in the import page's field reference.
     */
    public static function hints(): array
    {
        return [
            'company_type' => 'Limited, Sole Trader, Partnership, or Limited Liability Partnership',
            'business_start_date' => 'YYYY-MM-DD, not in the future',
            'date_of_birth' => 'YYYY-MM-DD, not in the future',
            'phone_no' => 'exactly 10 digits',
            'mobile_no' => 'exactly 10 digits',
            'email' => 'a valid email address',
            'gross_sales' => 'number',
            'funds_required' => 'number',
            'funds_term_months' => '12, 24, 36, 48, 60, or 72',
            'home_owner' => 'Yes or No',
            'vat_registered' => 'Yes or No',
            'loan_purpose' => 'Fund vehicle, equipment or machinery / Expansion / growth / Refinancing a loan / Tax payment / Working capital / Other',
            'postcode' => 'letters, numbers and spaces only, max 10 characters',
            'number_of_sites' => 'Single Site or Multiple Site',
            'sites_count' => '1-500, required when Number of Sites is Multiple Site',
            'mpan' => 'contain exactly 13 digits',
            'mprn' => 'contain between 6 and 8 digits',
            'spid' => 'contain between 8 and 10 digits',
            'status' => 'draft or published - leave blank to import as draft',
        ];
    }
}
