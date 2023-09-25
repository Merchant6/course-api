<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'payment_method_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
