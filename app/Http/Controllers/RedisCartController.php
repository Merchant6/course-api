<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Traits\Cart;


class RedisCartController extends Controller
{
    use Cart;

    public function __construct(protected Course $course)
    {

    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->user()->id;
        $pattern = "user:$userId:course:*"; 

        $cursor = 0;
        $fields = [];

        do 
        {
            [$cursor, $result] = Redis::scan($cursor, 'MATCH', $pattern);

            $fields = array_merge($fields, $result);

        } while ($cursor != 0);

        $cartData = [];
        foreach($fields as $field)
        {
            $cartData[] = Redis::hgetall($field);
        }

        return $cartData;

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
    public function destroy(string $id)
    {
        //
    }

    
}
