<?php

namespace App\Http\Middleware;

use App\Services\LanguageSelector;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
// use Illuminate\Support\Facades\Session;
// use Illuminate\Support\Facades\Cookie;

/**
 * @OA\OpenApi(
 *     @OA\Components(
 *         @OA\Parameter(
 *             parameter="AcceptLanguage",
 *             name="Accept-Language",
 *             in="header",
 *             required=false,
 *             @OA\Schema(type="string", example="en"),
 *             description="The language that will be set for any request"
 *         )
 *     )
 * )
 */
class SetLocale
{
    /**
     * @OA\Get(
     *     path="/api/{any}",
     *     summary="Set the current language by header",
     *     tags={"Languages"},
     *     @OA\Parameter(
     *         name="any",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Dynamic API path"
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(response=200, description="Language set successfully"),
     * )
     */
    public function getLanguage() {}

    /**
     * @OA\Post(
     *     path="/api/{any}",
     *     summary="Set the current language by header",
     *     tags={"Languages"},
     *     @OA\Parameter(
     *         name="any",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Dynamic API path"
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(response=200, description="Language set successfully"),
     * )
     */
    public function postLanguage() {}

    /**
     * @OA\Put(
     *     path="/api/{any}",
     *     summary="Set the current language by header",
     *     tags={"Languages"},
     *     @OA\Parameter(
     *         name="any",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Dynamic API path"
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(response=200, description="Language set successfully"),
     * )
     */
    public function putLanguage() {}

    public function handle(Request $request, Closure $next)
    {
        $isApi = $request->is('api/*') || $request->expectsJson();

        $selector = new LanguageSelector();
        $locale = $selector->detect($request, $isApi);

        App::setLocale($locale);

        $response = $next($request);

        // The language is saved in the session and cookies inside LanguageSelector,
        // but the cookie still needs to be explicitly attached to the response
        if (!$isApi) {
            $response->withCookie(cookie($selector->paramName, $locale, 525600));
        }

        return $response;
    }
}
