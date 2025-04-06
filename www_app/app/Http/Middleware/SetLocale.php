<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use App\Models\Language;
use Laravel\Sanctum\PersonalAccessToken;

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
     * @var bool
     * Indicates whether the locale has been set.
     */
    private $localeIsSet = false;

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
    public function getLanguage()
    {
    }

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
    public function postLanguage()
    {
    }

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
    public function putLanguage()
    {
    }
    
    public function handle(Request $request, Closure $next)
    {
        // Load the list of available languages from the database
        $enabledLanguages = Language::where('is_enabled', true)->pluck('code')->toArray();

        // If this is an API request, get the language from the token
        if ($request->is('api/*')) {
            $locale = $this->getApiLocale($request, $enabledLanguages);
            // Set the locale
            App::setLocale($locale);

            return $next($request);
        }

        // For web requests, check the standard sources
        foreach ([
            fn() => $request->get('lang'),
            fn() => Cookie::get('lang'),
            fn() => Session::get('lang'),
            fn() => $this->getAcceptedLanguage($enabledLanguages),
            fn() => config('app.locale'),
        ] as $resolver) {
            $locale = $resolver();
            if (in_array($locale, $enabledLanguages)) {
                break;
            }
        }
    
        // if (!$this->localeIsSet) {
            App::setLocale($locale);
        // }

        // Save the language in the session
        Session::put('lang', $locale);

        // Set the cookie for the language
        $response = $next($request);
        return $response->withCookie(cookie('lang', $locale, 525600)); // 1 year
    }

    /**
     * Get the language from the API token.
     */
    private function getApiLocale(Request $request, array $enabledLanguages): string
    {
        // Extract the token from the Authorization: Bearer <token> header
        $token = $request->bearerToken();
        if (!$token) {
            return $this->getDefaultApiLocale($request, $enabledLanguages);
        }

        // Get the user by token
        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken) {
            return $this->getDefaultApiLocale($request, $enabledLanguages);
        }

        $user = $accessToken->tokenable; // Get the user
        if (!$user || !in_array($user->language_code, $enabledLanguages)) {
            return $this->getDefaultApiLocale($request, $enabledLanguages);
        }

        return $user->language_code; // Return the saved language
    }

    private function getDefaultApiLocale(Request $request, array $enabledLanguages): string
    {
        $locale = $this->getAcceptedLanguage($enabledLanguages);
        return $locale ?? config('app.locale');
    }

    protected function getAcceptedLanguage(array $enabledLanguages): ?string
    {
        $acceptLanguage = request()->header('Accept-Language');
    
        if (!$acceptLanguage) {
            return null;
        }
    
        // Convert allowed languages to lowercase for comparison
        $enabledLanguages = array_map('strtolower', $enabledLanguages);
    
        $seen = [];
    
        foreach (explode(',', $acceptLanguage) as $lang) {
            $parts = explode(';q=', trim($lang));
            $locale = strtolower(trim($parts[0]));
            $short = substr($locale, 0, 2);
    
            if (strlen($short) < 2 || isset($seen[$short])) {
                continue;
            }
    
            $seen[$short] = true;
    
            if (in_array($short, $enabledLanguages, true)) {
                return $short;
            }
        }
    
        return null;
    }
}
