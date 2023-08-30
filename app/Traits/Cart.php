<?php

namespace App\Traits;
use App\Http\Requests\CartRequest;
use Illuminate\Support\Facades\Redis;

trait Cart
{

    /**
     * Add courses to cart 
     * @param \App\Http\Requests\CartRequest $request
     * @return bool
     */
    public function addToCart(CartRequest $request): bool
    {
        $data = $request->validated();

        $course = $this->course->findOrFail($data['course_id']);

        if(!$course)
        {
            return false;
        }

        $cartName = 'user:' . auth()->user()->id. ':course:'.$data['course_id'];
        $cartData = [
            'course_id' => $data['course_id'],
            'course_title' => $course->title,
            'course_price' => $course->price
        ];
        $addToSortedSet = Redis::hmset($cartName, $cartData);

        if($addToSortedSet)
        {
            return true;
        }

        return false;
    }

    /**
     * Return the cart total
     * @return float|int
     */
    public function cartTotal(): float|int
    {
        $userId = auth()->user()->id;
        $pattern = "user:$userId:course:*"; 

        $fields = $this->scanCart($pattern);

        $cartData = [];
        $cartPrice = 0;
        foreach($fields as $field)
        {
            $courseData = Redis::hgetall($field);
            if (isset($courseData['course_price'])) {
                $cartData[] = $courseData['course_price'];
            }

            $cartPrice += $courseData['course_price'];
        }

        return $cartPrice;
    }

    /**
     * Return the cart data saved in Redis hash
     * @param string $pattern
     * @return array
     */
    public function getCartData(string $pattern): array
    {
        $fields = $this->scanCart($pattern);

        $cartData = [];
        foreach($fields as $field)
        {
            $cartData[] = Redis::hgetall($field);
        }

        return $cartData;
    }

    public function deleteCartData(): array
    {
        $userId = auth()->user()->id;
        $pattern = "user:$userId:course:*"; 

        $fields = $this->scanCart($pattern);

        $deletedFields = [];
        foreach ($fields as $field) {
            $hashFields = Redis::hgetall($field);
            
            // Store the hash fields before deletion
            $deletedFields[$field] = $hashFields;
            
            // Delete the hash fields using HDEL
            Redis::hdel($field, array_keys($hashFields));
        }

        return $deletedFields;
    }

    /**
     * Scan the cart in redis for a given pattern
     * @param string $pattern
     * @return array
     */
    public function scanCart(string $pattern): array
    {
        $cursor = 0;
        $fields = [];

        do 
        {
            [$cursor, $result] = Redis::scan($cursor, 'MATCH', $pattern);

            $fields = array_merge($fields, $result);

        } while ($cursor != 0);

        return $fields;
    }
}