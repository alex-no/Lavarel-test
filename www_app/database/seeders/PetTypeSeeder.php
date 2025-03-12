<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PetType;

class PetTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $date = date('Y-m-d H:i:s');
        PetType::insert([
            ['name_uk' => 'собака', 'name_en' => 'dog', 'name_ru' => 'собака', 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'кішка', 'name_en' => 'cat', 'name_ru' => 'кошка', 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'гризун', 'name_en' => 'rodent', 'name_ru' => 'грызун', 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'птах', 'name_en' => 'bird', 'name_ru' => 'птица', 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'риба', 'name_en' => 'fish', 'name_ru' => 'рыба', 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'плазуне', 'name_en' => 'reptile', 'name_ru' => 'пресмыкающееся', 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'павук', 'name_en' => 'spider', 'name_ru' => 'паук', 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'комаха', 'name_en' => 'insect', 'name_ru' => 'насекомое', 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'інше', 'name_en' => 'other', 'name_ru' => 'другое', 'created_at' => $date, 'updated_at' => $date],
        ]);
    }
}
