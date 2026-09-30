<?php

namespace App\Http\Controllers;

use App\Services\CompaniesHouseService;
use App\Support\BusinessTypeMapper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\LeadDetail;

class CompaniesHouseController extends Controller
{
    protected CompaniesHouseService $companiesHouse;

    public function __construct(CompaniesHouseService $companiesHouse)
    {
        $this->companiesHouse = $companiesHouse;
    }

public function search(Request $request)
{
    $query = $request->get('q');

    if (!$query) {
        return response()->json([
            'success' => false,
            'message' => 'Search query is required.'
        ], 422);
    }

    try {

        // Companies House API call here
        $response = Http::withBasicAuth(
            config('services.companies_house.api_key'),
            ''
        )->get(
            'https://api.company-information.service.gov.uk/search/companies',
            [
                'q' => $query,
            ]
        );

        if (!$response->successful()) {

            return response()->json([
                'success' => false,
                'message' => 'Companies House API returned an error.',
                'status' => $response->status(),
                'response' => $response->body(),
            ], $response->status());
        }

        return response()->json([
            'success' => true,
            'data' => $response->json(),
        ]);

    } catch (\Throwable $e) {

        \Log::error('Companies House search failed', [
            'query' => $query,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}

/**
 * Company profile + officers for one company number. Served from
 * lead_details when we already hold it and it is fresh enough
 * (services.companies_house.cache_days); otherwise fetched from the
 * API and saved there for next time. If a refresh fails, the saved
 * (stale) copy is used rather than failing the form.
 */
public function show(Request $request, $companyNumber)
{
    try {

        $companyNumber = strtoupper(trim($companyNumber));

        if (!$companyNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Company number is required.'
            ], 400);
        }

        $leadDetail = LeadDetail::where('company_number', $companyNumber)->first();

        $hasSavedData = $leadDetail
            && $leadDetail->company_api_response
            && $leadDetail->officers_api_response;

        $cacheDays = (int) config('services.companies_house.cache_days', 30);
        $isFresh = $hasSavedData
            && $leadDetail->updated_at
            && $leadDetail->updated_at->gt(now()->subDays($cacheDays));

        if ($isFresh) {

            Log::info('Companies House data loaded from database', [
                'company_number' => $companyNumber,
            ]);

            return $this->companyResponse(
                $leadDetail->company_api_response,
                $leadDetail->officers_api_response,
                'database'
            );
        }

        Log::info($hasSavedData
            ? 'Companies House data in database is out of date. Calling API.'
            : 'Companies House data not found in database. Calling API.', [
            'company_number' => $companyNumber,
        ]);

        try {
            $company = $this->companiesHouse->getCompany($companyNumber);
            $officers = $this->companiesHouse->getOfficers($companyNumber);
        } catch (\Throwable $e) {

            if (!$hasSavedData) {
                throw $e;
            }

            Log::warning('Companies House refresh failed - using saved data', [
                'company_number' => $companyNumber,
                'error' => $e->getMessage(),
            ]);

            return $this->companyResponse(
                $leadDetail->company_api_response,
                $leadDetail->officers_api_response,
                'database'
            );
        }

        // touch() so an unchanged response still counts as refreshed.
        tap(LeadDetail::updateOrCreate(
            ['company_number' => $companyNumber],
            [
                'company_name' => $company['company_name'] ?? null,
                'company_type' => BusinessTypeMapper::companyTypeOption($company['type'] ?? null),
                'company_api_response' => $company,
                'officers_api_response' => $officers,
            ]
        ))->touch();

        return $this->companyResponse($company, $officers, 'api');

    } catch (\Throwable $e) {

        Log::error('Companies House details failed', [
            'company_number' => $companyNumber ?? null,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}

/**
 * The raw Companies House data plus what the lead form needs from it:
 * "type" as a readable Business Type label, and "company_type" - the
 * matching option of the form's Company Type dropdown (null when
 * there is none).
 */
private function companyResponse(array $company, array $officers, string $source)
{
    $rawType = $company['type'] ?? null;
    $company['type'] = BusinessTypeMapper::map($rawType);

    return response()->json([
        'success' => true,

        'data' => [
            'company' => $company,
            'officers' => $officers,
            'company_type' => BusinessTypeMapper::companyTypeOption($rawType),
        ],

        'source' => $source,
    ]);
}
}
