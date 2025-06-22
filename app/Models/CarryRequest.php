<?php

namespace App\Models;

use App\Enums\GeneralStatus;
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

    public function myApprovedOfferExists()
    {
        return $this->join('carry_request_offers', 'carry_request_offers.carry_request_id', 'carry_requests.id')
        ->join('travels', 'travels.id', 'carry_request_offers.travel_id')
        ->where(function($query){
            $query->where('travels.user_id', auth()->user()->id)
                ->orWhere('carry_request_offers.user_id', auth()->user()->id);
        })
        ->where('carry_request_offers.status', GeneralStatus::APPROVED)
        ->exists();
    }
}
