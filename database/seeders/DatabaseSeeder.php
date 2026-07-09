<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Only seed an initial super_admin when explicit credentials are
        // provided via the environment - never hardcode a real password
        // here, since this seeder runs against the production database.
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if ($email && $password) {
            User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => env('ADMIN_NAME', 'Admin'),
                    'password' => bcrypt($password),
                    'role' => 'super_admin',
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->call(TestimonialSeeder::class);
    }
}
