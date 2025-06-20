<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = ['match_id', 'sender_id', 'message', 'sent_at'];

    public function match(): BelongsTo { return $this->belongsTo(CarryRequestOffer::class); }
    public function sender(): BelongsTo { return $this->belongsTo(User::class, 'sender_id'); }
}
