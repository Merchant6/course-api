<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StripeProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'product_id'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function price()
    {
        return $this->hasOne(Price::class, 'product_id');
    }
}
