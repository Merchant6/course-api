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
    public function store(Request $request)
    {
        //coure_id, user_id, status, total_price, stripe_session_id
        $cartData = $this->cartTotal();

        return $cartData;

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
