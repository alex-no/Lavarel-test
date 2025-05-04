<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmMail;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

/**
 * @OA\Info(
 *     title="API Documentation",
 *     version="1.0.0",
 *     description="This is the API documentation for the Laravel application."
 * )
 */
class SiteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api",
     *     summary="API Root Info",
     *     tags={"About system"},
     *     @OA\Response(
     *         response="200",
     *         description="Information about version, language, and timezone",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="projectName", type="string"),
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="version", type="string"),
     *             @OA\Property(property="language", type="string"),
     *             @OA\Property(property="timeZone", type="string"),
     *         )
     *     )
     * )
     */
    public function index()
    {
        $app = app();
        return [
            'api' => 'Test API',
            'projectName' => config('app.name'),
            'id' => $app->environment(),
            'version' => $app->version(),
            'language' => $app->getLocale(),
            'timeZone' => config('app.timezone'),
        ];
    }
}
