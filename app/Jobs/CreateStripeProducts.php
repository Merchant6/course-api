<?php

namespace App\Jobs;

use App\Models\Course;
use App\Models\StripeProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stripe\StripeClient;

class CreateStripeProducts implements ShouldQueue, ShouldBeUnique
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
    public function handle(StripeClient $stripe, Course $course, StripeProduct $stripeProduct): void
    {
        $courses = $course->all(['id', 'title']);

        foreach($courses as $course)
        {
            $product = $stripe->products->create([
                'name' => $course->title,
            ]);

            $courseExists = $stripeProduct->where('course_id', $course->id)->exists();
            if(!$courseExists)
            {
                $data = $stripeProduct->create([
                    'course_id' => $course->id,
                    'product_id' => $product->id
                ]); 

                error_log($data);
            }

            if($courseExists)
            {
                error_log($course->id . " : Exists " . PHP_EOL);
            }
        }      
    }
}
