<?php

namespace App\Models;

use App\Enums\GeneralStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Travel extends Model
{
    use SoftDeletes;

    protected $table = 'travels';

    protected $fillable = [
        'user_id',
        'from_city_id',
        'to_city_id',
        'departure_date',
        'arrival_date',
        'airline',
        'notes',
        'weight_available',
        'weight_price'
    ];

    public function toCity(): BelongsTo { return $this->belongsTo(City::class, 'to_city_id'); }
    public function fromCity(): BelongsTo { return $this->belongsTo(City::class, 'from_city_id'); }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function offers(): HasMany { return $this->hasMany(CarryRequestOffer::class, 'travel_id'); }

    public function myApprovedOfferExists()
    {
        return $this->join('carry_request_offers', 'carry_request_offers.carry_request_id', 'travels.id')
            ->join('carry_requests', 'carry_requests.id', 'carry_request_offers.carry_request_id')
            ->where(function($query){
                $query->where('carry_requests.user_id', auth()->user()->id)
                    ->orWhere('carry_request_offers.user_id', auth()->user()->id);
            })
            ->where('carry_request_offers.status', GeneralStatus::APPROVED)
            ->exists();
    }
}
