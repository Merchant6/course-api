<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\StripeProduct;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Traits\Cart;
use Stripe\StripeClient;

class StripeSubscriptionController extends Controller
{
    use Cart;

    public function __construct(protected StripeClient $stripe, 
    protected Customer $customer, 
    protected StripeProduct $stripeProduct, 
    protected Subscription $subscription)
    {

    }

    public function createSubscription()
    {
        $userId = auth()->id();
        $pattern = "user:$userId:course:*";
        $cart = $this->getCartData($pattern);
        
        $customer = $this->customer->where('email', auth()->user()->email)->first(['id', 'customer_id']);
        foreach($cart as $product)
        {
            $stripeProduct = $this->stripeProduct
            ->with(['price' => function($q){
                $q->select(['id', 'product_id', 'price_id']);
            }])
            ->where('course_id', $product['course_id'])
            ->first(['id', 'course_id', 'product_id']);

            $priceId = $stripeProduct->price->price_id;

            $subscription = $this->stripe->subscriptions->create([
                                'customer' => $customer->customer_id,
                                'items' => [
                                ['price' => $priceId],
                            ],
                        ]);

            $createSubscription = $this->subscription->create([
                'customer_id' => $customer->id,
                'subscription_id' => $subscription->id
            ]);
            
        }
    }
}
