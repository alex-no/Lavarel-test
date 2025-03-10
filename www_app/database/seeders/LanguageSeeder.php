<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Language::insert([
            ['code' => 'uk', 'short_name' => 'Укр', 'full_name' => 'Українська', 'is_enabled' => true, 'order' => 1],
            ['code' => 'en', 'short_name' => 'Eng', 'full_name' => 'English', 'is_enabled' => true, 'order' => 2],
            ['code' => 'ru', 'short_name' => 'Рус', 'full_name' => 'Русский', 'is_enabled' => false, 'order' => 3],
        ]);
    }
}
