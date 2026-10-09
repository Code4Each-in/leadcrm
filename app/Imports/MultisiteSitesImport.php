<?php

namespace App\Imports;

/**
 * Reads a Multiple Site "sites CSV" (Supply Address, MPAN, MPRN, SPID -
 * one data row per site) into rows keyed by the slugged header
 * ("Supply Address" -> supply_address). Same comma delimiter and
 * string-only cells as LeadsImport, so a 13-digit MPAN is never
 * turned into a float. Validation lives in App\Support\MultisiteSitesCsv.
 */
class MultisiteSitesImport extends LeadsImport
{
}
