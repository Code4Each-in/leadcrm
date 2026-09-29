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
     * Import-only columns every product shares - not on the manual
     * form. user_name is resolved to the lead's intended Account
     * Manager by LeadCsvController (never stored as text).
     *
     * @return array<int,string>
     */
    public static function importFields(): array
    {
        return [
            'lead_date',
            'user_name',
        ];
    }

    /**
     * Field keys for the given product, in template column order -
     * common fields, then the product-specific panel, then the
     * import-only fields, then status.
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

        return array_merge(self::commonFields(), $productFields, self::importFields(), ['status']);
    }

    /**
     * What each column accepts, shown in the import page's Expected
     * Columns table. Every column is optional - a blank cell is skipped.
     */
    public static function hints(): array
    {
        return [
            'company_type' => 'Limited, Sole Trader, Partnership or Limited Liability Partnership.',
            'company_business_name' => 'Text, up to 255 characters.',
            'company_number' => 'Companies House number, up to 50 characters. Leading zeros are kept.',
            'business_start_date' => 'Date in YYYY-MM-DD format. Cannot be in the future.',
            'business_type' => 'Text, up to 255 characters.',
            'business_registered_address' => 'Text, up to 2,000 characters.',
            'business_trading_address' => 'Text, up to 2,000 characters.',
            'customer_name' => 'Text, up to 255 characters.',
            'contact_person' => 'Text, up to 255 characters.',
            'date_of_birth' => 'Date in YYYY-MM-DD format. Cannot be in the future.',
            'phone_no' => 'Exactly 10 digits.',
            'mobile_no' => 'Exactly 10 digits.',
            'email' => 'A valid email address.',
            'notes' => 'Text, up to 5,000 characters.',
            'gross_sales' => 'A number, 0 or more.',
            'funds_required' => 'A number, 0 or more.',
            'funds_term_months' => '12, 24, 36, 48, 60 or 72.',
            'home_owner' => 'Yes or No.',
            'vat_registered' => 'Yes or No.',
            'loan_purpose' => 'One of: Fund vehicle, equipment or machinery; Expansion / growth; Refinancing a loan; Tax payment; Working capital; Other.',
            'funds_usage_details' => 'Text, up to 2,000 characters.',
            'postcode' => 'Letters, numbers and spaces only, up to 10 characters.',
            'supply_address' => 'Text, up to 2,000 characters.',
            'number_of_sites' => 'Single Site or Multiple Site.',
            'sites_count' => 'Number of site leads to create, 1-500. Required for Multiple Site.',
            'mpan' => 'Exactly 13 digits, not already used by another lead. For Multiple Site, one MPAN per site separated by commas and wrapped in double quotes, e.g. "1234567890123,1234567890124".',
            'mprn' => '6 to 8 digits.',
            'spid' => '8 to 10 digits.',
            'lead_date' => 'Date in YYYY-MM-DD format. Cannot be in the future.',
            'user_name' => 'Exact full name or email address of an active Account Manager for this product. The lead is assigned to them when it is published.',
            'status' => 'draft or published. Leave blank to import as a draft.',
        ];
    }
}
