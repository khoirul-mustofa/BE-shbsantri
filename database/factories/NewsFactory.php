<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

// Contoh mengonversi waktu ke zona waktu tertentu
//        $date = Carbon::now('Asia/Jakarta');

        return [
            'title' => fake()->sentence(),
            'content' => fake()->paragraph(),
            'image' => fake()->imageUrl(),
            'video' => fake()->imageUrl(),
            'pdf' => fake()->imageUrl(),
            'is_published' => 0,
            'category_id' => 1,
            'user_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),

        ];
    }
}
