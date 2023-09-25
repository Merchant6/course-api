<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class StripePaymentMethodController extends Controller
{
    public function __construct(protected StripeClient $stripe, 
    protected PaymentMethod $paymentMethod, 
    protected Customer $customer)
    {
        
    }

    public function createPaymentMethod(Request $request)
    {
        $stripePaymentMethod = $this->stripe->paymentMethods->create([
            'type' => 'card',
            'card' => [
                'token' => $request->token
            ],
        ]);

        $customer = $this->customer->where('email', auth()->user()->email)->first(['customer_id']);
        $attachPaymentMethod = $this->stripe->paymentMethods->attach(
            $stripePaymentMethod->id,
            ['customer' => $customer->customer_id]
        );

        $paymentMethodExists = $this->paymentMethod->where('user_id', auth()->user()->id)->exists();
        if(!$paymentMethodExists)
        {
            $this->paymentMethod->create([
                'user_id' => auth()->user()->id,
                'payment_method_id' => $stripePaymentMethod->id
            ]);
    
    
            return response()->json([
                'message' => 'Payment method created successfully.'
            ]);
        }

        return response()->json([
            'message' => 'Payment method already exists.'
        ]);

    }
}
