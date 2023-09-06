<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'price_id'
    ];

    public function stripeProduct()
    {
        return $this->belongsTo(StripeProduct::class);
    }
}
