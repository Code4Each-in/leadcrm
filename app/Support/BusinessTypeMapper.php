<?php

namespace App\Support;

/**
 * Normalizes the raw "type" codes Companies House returns (e.g. "ltd",
 * "llp", "plc") into the labels the CRM should display/store. Anything
 * not in the map - including whatever a user typed in by hand - is
 * passed through unchanged, so unknown values don't disappear or get
 * overwritten with something wrong.
 *
 * Codes are Companies House's company_type enumeration:
 * https://github.com/companieshouse/api-enumerations/blob/master/constants.yml
 */
class BusinessTypeMapper
{
    private const MAP = [
        // "Limited" (not the longer Companies House wording) is what
        // leads have always stored for a private limited company.
        'ltd' => 'Limited',
        'plc' => 'Public Limited Company',
        'llp' => 'Limited Liability Partnership',
        'limited-partnership' => 'Limited Partnership',
        'scottish-partnership' => 'Scottish Partnership',
        'private-unlimited' => 'Private Unlimited Company',
        'private-unlimited-nsc' => 'Private Unlimited Company without Share Capital',
        'private-limited-guarant-nsc' => 'Private Limited by Guarantee without Share Capital',
        'private-limited-guarant-nsc-limited-exemption' => "Private Limited by Guarantee without Share Capital ('Limited' exemption)",
        'private-limited-shares-section-30-exemption' => "Private Limited Company ('Limited' exemption)",
        'old-public-company' => 'Old Public Company',
        'community-interest-company' => 'Community Interest Company',
        'charitable-incorporated-organisation' => 'Charitable Incorporated Organisation',
        'scottish-charitable-incorporated-organisation' => 'Scottish Charitable Incorporated Organisation',
        'industrial-and-provident-society' => 'Industrial and Provident Society',
        'registered-society-non-jurisdictional' => 'Registered Society',
        'royal-charter' => 'Royal Charter Company',
        'investment-company-with-variable-capital' => 'Investment Company with Variable Capital',
        'icvc-securities' => 'Investment Company with Variable Capital (Securities)',
        'icvc-warrant' => 'Investment Company with Variable Capital (Warrant)',
        'icvc-umbrella' => 'Investment Company with Variable Capital (Umbrella)',
        'protected-cell-company' => 'Protected Cell Company',
        'assurance-company' => 'Assurance Company',
        'eeig' => 'European Economic Interest Grouping',
        'european-public-limited-liability-company-se' => 'European Public Limited Liability Company (SE)',
        'oversea-company' => 'Overseas Company',
        'uk-establishment' => 'UK Establishment of an Overseas Company',
        'registered-overseas-entity' => 'Registered Overseas Entity',
        'unregistered-company' => 'Unregistered Company',
        'northern-ireland' => 'Northern Ireland Company',
        'northern-ireland-other' => 'Northern Ireland Company (Other)',
        'further-education-or-sixth-form-college-corporation' => 'Further Education or Sixth Form College Corporation',
        'converted-or-closed' => 'Converted / Closed',
        'other' => 'Other',
    ];

    /**
     * Which option of the lead form's Company Type dropdown a
     * Companies House type belongs to. Types with no matching option
     * (a charity, an overseas company, ...) are left for the user.
     */
    private const COMPANY_TYPE_OPTIONS = [
        'ltd' => 'Limited',
        'plc' => 'Limited',
        'private-limited-guarant-nsc' => 'Limited',
        'private-limited-guarant-nsc-limited-exemption' => 'Limited',
        'private-limited-shares-section-30-exemption' => 'Limited',
        'old-public-company' => 'Limited',
        'llp' => 'Limited Liability Partnership',
        'limited-partnership' => 'Partnership',
        'scottish-partnership' => 'Partnership',
    ];

    public static function map(?string $type): ?string
    {
        if ($type === null || trim($type) === '') {
            return $type;
        }

        return self::MAP[self::key($type)] ?? $type;
    }

    public static function companyTypeOption(?string $type): ?string
    {
        if ($type === null || trim($type) === '') {
            return null;
        }

        return self::COMPANY_TYPE_OPTIONS[self::key($type)] ?? null;
    }

    private static function key(string $type): string
    {
        return strtolower(trim($type));
    }
}
