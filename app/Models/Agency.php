<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agency extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'agency_name',
        'primary_contact_name',
        'primary_email',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'logo'
    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
