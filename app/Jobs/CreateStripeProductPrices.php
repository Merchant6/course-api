<?php

namespace App\Jobs;

use App\Models\Price;
use App\Models\StripeProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stripe\StripeClient;

class CreateStripeProductPrices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(StripeClient $stripe, StripeProduct $stripeProduct): void
    {   
            $products = $stripeProduct->all(['id', 'course_id', 'product_id']);
            foreach($products as $product)
            {
                $price = $stripe->prices->create([
                    'unit_amount' => $product->course->price,
                    'currency' => 'usd',
                    'recurring' => [
                        'interval' => 'week',
                        'interval_count' => 3
                    ],
                    'product' => $product->product_id,
                ]);


                $productPrice = new Price();
                $productPrice->product_id = $product->id;
                $productPrice->price_id = $price->id;
                $productPrice->save();     
                
                error_log($price . PHP_EOL);
                // error_log('Saving the data' . $createPrice . PHP_EOL);
                // error_log($createPrice . PHP_EOL);

            }
    }
}
