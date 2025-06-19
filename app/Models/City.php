<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    protected $table = 'cities';

    protected $fillable = [
        'name',
        'country_id',
        'city_type'
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
