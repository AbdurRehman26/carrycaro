<?php

namespace App\Models;

use App\Enums\GeneralStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarryRequestOffer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'carry_request_id', 'trip_id', 'status', 'message', 'user_id'
    ];

    public function CarryRequest(): BelongsTo { return $this->belongsTo(CarryRequest::class); }

    public function trip(): BelongsTo { return $this->belongsTo(Trip::class); }

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

    public function canSeeEachOtherDetails(): bool
    {
        return $this->status == GeneralStatus::APPROVED &&
            (
                auth()->user()->id == $this->carryRequest->user_id  ||
                auth()->user()->id == $this->trip->user_id
            );
    }
}
