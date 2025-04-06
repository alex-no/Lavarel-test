<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmMail;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

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

    public function checkEmailSend()
    {
        //$email = 'test@example.com';
        $email = 'alex@4n.com.ua';
        $name = 'Alex';
        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => 1, 'hash' => sha1('test')]
        );

        Mail::to($email)->send(new ConfirmMail($name, $verifyUrl));
        return response()->json([
            'message' => 'Email sent successfully'
        ], 200);
    }
}
