<?php

namespace App\Models;

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
    public function matches(): HasMany { return $this->hasMany(DeliveryMatch::class, 'travel_id'); }
}
