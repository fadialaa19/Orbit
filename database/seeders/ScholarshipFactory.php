<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Scholarship>
 */
class ScholarshipFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title_ar' => fake()->words(4, true),
            'country' => fake()->country(),
            'university' => fake()->words(2, true),
            'deadline' => fake()->dateTimeBetween('+1 week', '+6 months'),
            'description' => fake()->paragraph(),
            'category' => fake()->word(),
            'status' => fake()->randomElement(['active', 'closed']),
            'coverage' => json_encode(fake()->randomElements(['الرسوم الدراسية', 'السكن', 'التأمين', 'تذكرة الطيران'], 2)),
            'tags' => json_encode(fake()->words(3)),
        ];
    }
}

