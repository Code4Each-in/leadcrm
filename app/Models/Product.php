<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Product extends Model
{
    protected $fillable = [
        'name',
    ];

    public $timestamps = false;
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}

