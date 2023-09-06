<?php

namespace App\Http\Controllers;

use App\Jobs\CreateStripeProducts;
use App\Models\StripeProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class StripeProductController extends Controller
{
    public function __construct(protected StripeClient $stripe, protected StripeProduct $stripeProduct, protected CreateStripeProducts $products)
    {

    }

    public function createProduct(): JsonResponse
    {
        $this->products->dispatch();

        return response()->json([
            'message' => 'Creating Stripe Products, job started...',
            'Note' => 'If you are creating a existing product again, it will not create the product in the stripe dashboard.'
        ], 200);
    }
}
