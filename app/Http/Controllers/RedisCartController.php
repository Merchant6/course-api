<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Traits\Cart;
use Illuminate\Support\Facades\Response;


class RedisCartController extends Controller
{
    use Cart;

    public function __construct(protected Course $course)
    {

    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $userId = auth()->id();
        $pattern = "user:$userId:course:*"; 

        $cartData = $this->getCartData($pattern);

        if($cartData)
        {
            return response()->json([
                'cart' => $cartData
            ], 200);
        }

        return response()->json([
            'message' => 'Cart is empty.'
        ], 404);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CartRequest $request)
    {
        try {
           
            if($this->addToCart($request))
            {
                return response()->json([
                    'message' => 'Course added to cart.'
                ], 200);
            }

            return response()->json([
                'error' => 'There was an error adding your course to cart, try again later.'
            ], 404);
            
        } 
        catch (\Exception $e) {
            return $e->getMessage();
        }
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
    public function destroy()
    {       
        $deleted = $this->deleteCartData(auth()->id());

        if($deleted)
        {
            return response()->json([
                'message' => 'Cart data cleared.'
            ], 200);
        }

        return response()->json([
            'message' => 'Cart is already cleared.'
        ], 404);
    }

    
}
