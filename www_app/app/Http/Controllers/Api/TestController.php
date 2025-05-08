<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmMail;
use Carbon\Carbon;
use ServerTimeClock\ServerClock;


class TestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/check-db",
     *     summary="List database tables",
     *     tags={"Test"},
     *     @OA\Response(
     *         response="200",
     *         description="Returns list of table names",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Database connection is successful"
     *             ),
     *             @OA\Property(
     *                 property="tables",
     *                 type="array",
     *                 @OA\Items(type="string")
     *             )
     *         )
     *     )
     * )
     */
    public function checkDatabaseConnection()
    {
        try {
            // Checking the connection
            DB::connection()->getPdo();

            // Retrieve the list of tables
            $tables = DB::select('SHOW TABLES');
            // $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");

            return response()->json([
                'message' => 'Database connection is successful',
                'tables' => array_map('current', $tables) // Convert the result into a simple array
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Could not connect to the database. Please check your configuration.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

  /**
     * @OA\Get(
     *     path="/api/server-clock",
     *     summary="Get current server time",
     *     tags={"Test"},
     *     description="Returns the current server time and timezone",
     *     @OA\Response(
     *         response=200,
     *         description="Current server time information",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="clientName",
     *                 type="string",
     *                 example="IpGeoLocation"
     *             ),
     *             @OA\Property(
     *                 property="now",
     *                 type="string",
     *                 format="date-time",
     *                 example="2025-05-08 12:51:35"
     *             ),
     *             @OA\Property(
     *                 property="timezone",
     *                 type="string",
     *                 example="Europe/Kyiv"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Clock service error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unable to reach clock service")
     *         )
     *     )
     * )
     */
    public function checkServerClock(ServerClock $clock)
    {
        try {
            // $clock = app(ServerClock::class);
            $data = [
                'clientName' => $clock->getClientName(),
                'now' => $clock->now()->format('Y-m-d H:i:s'),
                'timezone' => $clock->getTimezone()->getName(),
            ];
            // $data = $clock->getData($clock);

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to reach clock service',
                'details' => $e->getMessage()
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
