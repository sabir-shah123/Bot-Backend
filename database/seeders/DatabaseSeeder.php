<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            "first_name" => "Muhammad",
            "last_name" => "Sabir",
            "email" => 'superadmin@gmail.com',
            "role" => 0,
            "password" => '12345678',
            "email_verified_at" => Carbon::now(),
            'status' => true
        ]);
    }
}
