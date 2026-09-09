<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadDetail extends Model
{
    protected $table = 'lead_details';

    protected $fillable = [
        'company_number',
        'company_name',
        'company_type',
        'company_api_response',
        'officers_api_response',
    ];

    protected $casts = [
        'company_api_response' => 'array',
        'officers_api_response' => 'array',
    ];
}
