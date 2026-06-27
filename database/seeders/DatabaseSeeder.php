<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Scholarship;
use App\Models\Plan;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        \App\Models\User::factory()->create([
        'name' => 'Eng Fadi Alaa',
        'email' => 'fadi@test.com', // الإيميل الذي ستدخل به
        'password' => bcrypt('12345678'), // الباسورد
        'role' => 'super_admin',
    ]);
    }
}

    


