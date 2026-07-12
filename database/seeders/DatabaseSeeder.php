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
        $email = config('app.admin.email');
        $password = config('app.admin.password');

        if ($email && $password) {
            User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => config('app.admin.name'),
                    'password' => bcrypt($password),
                    'role' => 'super_admin',
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->call(TestimonialSeeder::class);
    }
}
