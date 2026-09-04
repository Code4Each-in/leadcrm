<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CompaniesHouseService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.companies_house.base_url');
        $this->apiKey = config('services.companies_house.api_key');
    }

    /**
     * Search Companies House
     */
    public function searchCompanies(string $query)
    {
        return Http::withBasicAuth($this->apiKey, '')
            ->get($this->baseUrl . '/search/companies', [
                'q' => $query,
            ])
            ->throw()
            ->json();
    }

    /**
     * Get company information
     */
    public function getCompany(string $companyNumber)
    {
        $response = Http::withBasicAuth($this->apiKey, '')
            ->get(
                $this->baseUrl . '/company/' . trim($companyNumber)
            );

        if (!$response->successful()) {
            throw new \Exception(
                'Companies House API Error: ' .
                $response->status() .
                ' - ' .
                $response->body()
            );
        }

        return $response->json();
    }

    /**
     * Get company officers
     */
    public function getOfficers(string $companyNumber)
    {
        $response = Http::withBasicAuth($this->apiKey, '')
            ->get(
                $this->baseUrl .
                '/company/' .
                trim($companyNumber) .
                '/officers'
            );

        if (!$response->successful()) {
            throw new \Exception(
                'Companies House Officers API Error: ' .
                $response->status() .
                ' - ' .
                $response->body()
            );
        }

        return $response->json();
    }
}
