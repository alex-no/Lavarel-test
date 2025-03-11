<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            ['name' => 'John Doe', 'email' => 'john.doe@example.com', 'password' => '"$2y$12$pWadKZAklXYWcdg84d1sR.oya.hJII4DQOiQvyAXHgHclQYDEjeqm', 'language_code' => "uk", 'created_at' => "2025-03-10 23:21:26", 'updated_at' => "2025-03-11 04:48:17"],
        ]);
    }
}
