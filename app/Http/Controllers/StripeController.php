<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Order;
use App\Traits\Cart;
use Illuminate\Http\Request;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\StripeClient;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StripeController extends Controller
{   
    use Cart;

    public function __construct(
        protected StripeClient $stripe,
        protected Order $order,
        )
    {

    }

    public function checkout()
    {
        try {

            //Get the current user cart from Redis
            $userId = auth()->user()->id;
            $pattern = "user:$userId:course:*";
            $products = $this->getCartData($pattern);

            //Get the unpiad Order
            $order = $this->order->where('user_id', $userId)->where('status', 'pending')->latest()->first();

            $lineItems = [];
            $totalPrice = $this->cartTotal();

            foreach ($products as $product) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $product['course_title'],
                            // 'images' => 'N/A'
                        ],
                        'unit_amount' => $product['course_price'] * 100,
                    ],
                    'quantity' => 1,
                ];
            }
            
            $session = $this->stripe->checkout->sessions->create([
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('success', [], true) . "?session_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => route('cancel', [], true),
            ]);


            $order->status = 'unpaid';
            $order->total_price = $totalPrice;
            $order->stripe_session_id = $session->id;
            $order->save();

            return response()->json([
                'redirectTo' => $session->url,
            ]);
            
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function success(Request $request)
    {

        $sessionId = $request->get('session_id');
        $customer = null;

        try
        {
            $session = $this->stripe->checkout->sessions->retrieve($sessionId);
            if(!$session)
            {
                throw new NotFoundHttpException();
            }
            $customer = $this->stripe->customers->retrieve($session->customer);

            $order = Order::where('stripe_session_id', $session->id)->first();
            if (!$order) {
                throw new NotFoundHttpException();
            }
            if ($order->status === 'unpaid') {
                $order->status = 'paid';
                $order->save();
            }

            return response()->json([
                'success' => 'Payment Success',
                'customer' => $customer->name,
                'session_id' => $order->stripe_session_id,
                'order_status' => $order->status
            ]);
        }
        catch(\Exception $e)
        {
            throw new NotFoundHttpException();
        }

    }

    public function cancel()
    {
        return response()->json([
            'cancel' => 'Payment cancelled'
        ]);
    }
}
