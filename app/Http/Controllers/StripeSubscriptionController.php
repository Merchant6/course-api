<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\StripeProduct;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Traits\Cart;
use Stripe\StripeClient;

class StripeSubscriptionController extends Controller
{
    use Cart;

    public const ACTIVE = 'active';
    public const CANCELED = 'canceled';

    public function __construct(protected StripeClient $stripe, 
    protected Customer $customer, 
    protected StripeProduct $stripeProduct, 
    protected Subscription $subscription,
    protected PaymentMethod $paymentMethod,)
    {

    }

    public function createSubscription()
    {
        $userId = auth()->id();
        $pattern = "user:$userId:course:*";
        $cart = $this->getCartData($pattern);
        
        $customer = $this->customer->where('email', auth()->user()->email)->first(['id', 'customer_id']);

        $items = [];
        foreach($cart as $product)
        {
            $stripeProduct = $this->stripeProduct
            ->with(['price' => function($q){
                $q->select(['id', 'product_id', 'price_id']);
            }])
            ->where('course_id', $product['course_id'])
            ->first(['id', 'course_id', 'product_id']);

            $priceId = $stripeProduct->price->price_id;

            $items[] = ['price' => $priceId];
            
        }
        
        $paymentMethod = $this->paymentMethod->where('user_id', auth()->user()->id)->first(['payment_method_id']); 

        $subscription = $this->stripe->subscriptions->create([
            'customer' => $customer->customer_id,
            'items' => $items,
            'default_payment_method' => $paymentMethod->payment_method_id
        ]);

        $createSubscription = $this->subscription->create([
            'customer_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'status' => self::ACTIVE
        ]);

        if($subscription && $createSubscription)
        {
            return response()->json([
                'message' => 'Your subscription is created successfully, your subscription id is ' . $subscription->id,
            ], 200);
        }

        return response()->json([
            'message' => 'There was an issue creating your subscription, try again later.',
        ], 400);
    }

    public function listSubscriptions()
    {
        $customerId = $this->customer
        ->where('email', auth()->user()->email)
        ->first(['id', 'customer_id']);

        $customer = $customerId->customer_id;

        $subscriptions = $this->stripe->subscriptions->all([
            'customer' => $customer
        ]);

        return $subscriptions->data;
    }

    public function cancelSubscription(Request $request)
    {   
        $subscriptionId = $request->subscription_id;

        $subscription = $this->subscription
        ->where('subscription_id', $subscriptionId)
        ->where('status', self::ACTIVE)
        ->first(['subscription_id', 'status']);

        // return $subscription;

        if($subscription)
        {
            $this->stripe->subscriptions->cancel(
                $subscriptionId,
                []
            );

            $this->subscription
            ->where('subscription_id', $subscriptionId)
            ->update([
                'status' => self::CANCELED
            ]);

            return response()->json([
                'message' => 'Subscription cancelled successfully.',
                'status' => self::CANCELED
            ], 200);
        }
        
        return response()->json([
            'message' => 'Subscription cannot be found.'
        ], 404);
    }

    // public function resumeSubscription(Request $request)
    // {
    //     $subscriptionId = $request->subscription_id;

    //     $subscription = $this->subscription
    //     ->where('subscription_id', $subscriptionId)
    //     ->where('status', self::CANCELED)
    //     ->first(['subscription_id', 'status']);

    //     if($subscription)
    //     {
    //         $this->stripe->subscriptions->resume(
    //             $subscriptionId,
    //             ['billing_cycle_anchor' => 'now']
    //         );

    //         $this->subscription
    //         ->where('subscription_id', $subscriptionId)
    //         ->update([
    //             'status' => self::ACTIVE,
    //         ]);

    //         return response()->json([
    //             'message' => 'Subscription activated successfully.',
    //             'status' => self::ACTIVE
    //         ], 200);
    //     }

    //     return response()->json([
    //         'message' => 'Subscription cannot be found.'
    //     ], 404);
    // }
}
