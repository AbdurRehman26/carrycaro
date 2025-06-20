<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarryRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'from_city_id',
        'to_city_id',
        'receiver_name',
        'receiver_number',
        'for_self',
        'preferred_date',
        'delivery_deadline',
        'status',
        'user_id',
        'note',
        'weight',
        'price'
    ];

    public function fromCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'from_city_id');
    }

    public function toCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'to_city_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(Product::class, CarryRequestProduct::class, 'carry_request_id', 'id');
    }
    public function offers(): HasMany { return $this->hasMany(CarryRequestOffer::class); }

    public function myOffer(): HasOne { return $this->hasOne(CarryRequestOffer::class)->where('user_id', auth()->user()->id); }
}
