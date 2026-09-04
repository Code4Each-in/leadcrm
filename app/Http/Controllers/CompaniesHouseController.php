<?php

namespace App\Http\Controllers;

use App\Services\CompaniesHouseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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


public function show($companyNumber)
{
    try {

        $companyNumber = trim($companyNumber);

        if (!$companyNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Company number is required.'
            ], 400);
        }

        /*
         * First API:
         * Company Information
         */
        $company = $this->companiesHouse
            ->getCompany($companyNumber);

        /*
         * Second API:
         * Company Officers
         */
        $officers = $this->companiesHouse
            ->getOfficers($companyNumber);

        return response()->json([
            'success' => true,

            'data' => [
                'company' => $company,
                'officers' => $officers,
            ],
        ]);

    } catch (\Throwable $e) {

        \Log::error('Companies House details failed', [
            'company_number' => $companyNumber ?? null,
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}
}
