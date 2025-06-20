<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarryRequestProduct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'carry_request_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function carryRequest()
    {
        return $this->belongsTo(CarryRequest::class);
    }
}
