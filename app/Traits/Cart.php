<?php

namespace App\Traits;
use App\Http\Requests\CartRequest;
use Illuminate\Support\Facades\Redis;

trait Cart
{

    public function addToCart(CartRequest $request)
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

    public function cartTotal()
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
}