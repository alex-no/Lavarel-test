<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmMail;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class TestController extends Controller
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

    public function checkEmailSend()
    {
        $email = 'alex@4n.com.ua';
        $name = 'Alex';
        $verifyUrl = 'http://example.com/verify-email?' . http_build_query([
            'id' => 1,
            'email' => $email,
            'expires' => Carbon::now()->addMinutes(60)->timestamp,
            'signature' => hash_hmac('sha256', $email, config('app.key')),
        ]);

        Mail::to($email)->send(new ConfirmMail($name, $verifyUrl));
        return response()->json([
            'message' => 'Email sent successfully'
        ], 200);
    }
}
