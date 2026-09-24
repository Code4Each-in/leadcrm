<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Product extends Model
{
    /**
     * Seeded by ProductSeeder in insertion order (NFS, AF4U, AU
     * Savers) - Pricing is gated to this id rather than a name
     * comparison, matching the app's move to id-based checks
     * elsewhere (see config/roles.php).
     */
    public const AU_SAVERS_ID = 3;

    protected $fillable = [
        'name',
    ];

    public $timestamps = false;
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}

