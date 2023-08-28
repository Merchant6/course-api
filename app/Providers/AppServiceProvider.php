<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(StripeClient::class, function ($app) {
            $stripe = new StripeClient(env('STRIPE_SECRET'));
            return $stripe;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
