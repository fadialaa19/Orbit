<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SupportTicket>
 */
class SupportTicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'subject' => fake()->sentence(),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'emergency']),
            'status' => fake()->randomElement(['pending', 'open', 'closed']),
            'messages' => json_encode([
                [
                    'message' => fake()->paragraph(),
                    'sender' => 'user',
                    'timestamp' => now()->subHours(rand(1, 48))->toISOString(),
                ]
            ]),
            'last_reply_at' => now()->subHours(rand(0, 24)),
        ];
    }
}

