<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarryRequestOffer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'carry_request_id', 'travel_id', 'status', 'message', 'user_id'
    ];

    public function CarryRequest(): BelongsTo { return $this->belongsTo(CarryRequest::class); }

    public function travel(): BelongsTo { return $this->belongsTo(Travel::class); }

    public function approve(): void
    {
        $this->status = 'approved';
        $this->save();
    }

    public function reject(): void
    {
        $this->status = 'rejected';
        $this->save();
    }

    public function messages(): HasMany { return $this->hasMany(Message::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
