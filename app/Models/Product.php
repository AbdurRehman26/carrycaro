<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'product_name', 'product_link', 'product_description',
        'store_name', 'store_location', 'price', 'image_url'
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function deliveryRequest(): HasOne { return $this->hasOne(DeliveryRequest::class);}
}
