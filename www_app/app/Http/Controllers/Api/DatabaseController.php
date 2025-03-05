<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    public function checkDatabaseConnection()
    {
        try {
            // Проверяем соединение
            DB::connection()->getPdo();

            // Получаем список таблиц
            $tables = DB::select('SHOW TABLES');
            // $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");

            return response()->json([
                'message' => 'Database connection is successful',
                'tables' => array_map('current', $tables) // Преобразуем результат в простой массив
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Could not connect to the database. Please check your configuration.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
