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
            $products = Course::all();
            $lineItems = [];
            $totalPrice = $this->cartTotal();
            // foreach ($products as $product) {
            //     $totalPrice += $product->price;
            //     $lineItems[] = [
            //         'price_data' => [
            //             'currency' => 'usd',
            //             'product_data' => [
            //                 'name' => $product->name,
            //                 'images' => [$product->image]
            //             ],
            //             'unit_amount' => $product->price * 100,
            //         ],
            //         'quantity' => 1,
            //     ];
            // }
            
            // $session = $this->stripe->checkout->sessions->create([
            //     'line_items' => $lineItems,
            //     'mode' => 'payment',
            //     'success_url' => route('checkout.success', [], true) . "?session_id={CHECKOUT_SESSION_ID}",
            //     'cancel_url' => route('checkout.cancel', [], true),
            // ]);

            // $this->order->status = 'unpaid';
            // $this->order->total_price = $totalPrice;
            // $this->order->session_id = $session->id;
            // $this->order->save();

            // return response()->json([
            //     'redirectTo' => $session->url,
            // ]);
            
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
                'session_id' => cache('order')['session_id'],
                'order_status' => cache('order')['order_status']
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
