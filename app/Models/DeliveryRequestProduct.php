<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryRequestProduct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'delivery_request_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function deliveryRequest()
    {
        return $this->belongsTo(DeliveryRequest::class);
    }
}
