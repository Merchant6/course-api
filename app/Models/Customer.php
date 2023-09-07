<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'customer_id'
    ];

    public function subscription()
    {
        return $this->hasMany(Subscription::class);
    }
}
