<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class StripeUserController extends Controller
{
    public function __construct(protected StripeClient $stripe, protected Customer $customer)
    {

    }

    public function createCustomer(): JsonResponse
    {
        $user = auth()->user();
        $customerExists = $this->customer->where('email', $user->email)->exists();

        if(!$customerExists)
        {
            $customer = $this->stripe->customers->create([
                'email' => $user->email,
                'name' => $user->name 
            ]);

            $this->customer->create([
                'email' => $user->email,
                'name' => $user->name,
                'customer_id' => $customer->id
            ]);

            return response()->json([
                'details' => 'Customer created successfully.'
            ], 200);
        }

        return response()->json([
            'error' => 'Cutomer already exists.'
        ], 400);
    }
}
