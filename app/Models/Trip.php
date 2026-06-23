<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use SoftDeletes;

    protected $table = 'trips';

    protected $fillable = [
        'user_id',
        'from_city_id',
        'to_city_id',
        'departure_date',
        'arrival_date',
        'airline_id',
        'airline',
        'notes',
        'weight_available',
        'weight_price',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'arrival_date' => 'date',
    ];

    public function toCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'to_city_id');
    }

    public function fromCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'from_city_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function airlineRecord(): BelongsTo
    {
        return $this->belongsTo(Airline::class, 'airline_id');
    }
}
