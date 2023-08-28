<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Traits\Cart;

class OrderController extends Controller
{
    use Cart;

    public function __construct(protected Order $model)
    {

    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->model->where('user_id', auth()->user()->id)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        $cartTotal = $this->cartTotal();

        //Create the order before payment
        $createOrder = $this->model->create([
            'user_id' => auth()->user()->id,
            'status' => 'pending',
            'total_price' => $cartTotal,
        ]);

        if($createOrder)
        {
            return response()->json([
                'message' => 'Your order has been created. Redirecting to checkout to complete your transaction.',
                'checkout' => route('checkout')                
            ], 200);
        }

        return response()->json([
            'error' => 'There was an error creating your order.',               
        ], 404);


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
