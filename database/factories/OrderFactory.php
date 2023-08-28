<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'course_id' => Course::get()->random()->id,
            'status' => fake()->randomElement(['paid', 'unpaid', 'pending']),
            // 'total_price' => 
            // 'stripe_session_id'
        ];
    }
}
