<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DevelopmentPlanAddSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('development_plan')->insert([
            'sort_order' => 5,
            'status' => 'in_progress',
            'feature_en' => 'Frontend for development plan',
            'feature_uk' => 'Frontend для плану розробки',
            'feature_ru' => 'Frontend для плана разработки',
            'technology_en' => 'PHP, Laravel 12, Vue 3, bootstrap',
            'technology_uk' => 'PHP, Laravel 12, Vue 3, bootstrap',
            'technology_ru' => 'PHP, Laravel 12, Vue 3, bootstrap',
            'result_en' => 'Displaying the development plan as a table with a language switcher',
            'result_uk' => 'Відображення плану розробки у вигляді таблиці з перемикачем мови',
            'result_ru' => 'Отображение плана разработки в виде таблицы, с переключателем языка',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
