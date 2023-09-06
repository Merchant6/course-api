<?php

namespace App\Http\Controllers;

use App\Jobs\CreateStripeProductPrices;
use App\Models\Course;
use App\Models\Price;
use App\Models\StripeProduct;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class StripePriceController extends Controller
{
    public function __construct(protected StripeClient $stripe, protected CreateStripeProductPrices $prices)
    {

    }

    public function createPrice(Request $request)
    {
        
        try
        {
            $this->prices->dispatch();
            // $products = $this->stripeProduct->all(['id', 'course_id', 'product_id']);

            // foreach($products as $product)
            // {
            //     error_log($product->course->price . PHP_EOL);
                

            //     $price = $this->stripe->prices->create([
            //         'unit_amount' => $product->course->price,
            //         'currency' => 'usd',
            //         'recurring' => [
            //             'interval' => 'week',
            //             'interval_count' => 3
            //         ],
            //         'product' => $product->product_id,
            //     ]);


            //     // $createPrice = $price->create([
            //     //     'course_id' => $product->course->id,
            //     //     'price_id' => $price->id,
            //     // ]);        

            //     // error_log($product->product_id . PHP_EOL);
            //     // error_log($createPrice . PHP_EOL);

            //     return $price;
            // }
        }
        catch(\Exception $e)
        {
            return $e->getMessage();
        }
    }    
}
