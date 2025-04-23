<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class LanguageSelector
{
    public string $paramName = 'lang';
    public string $userAttribute = 'language_code';
    public string $default = 'en';
    public string $tableName = 'languages';
    public string $codeField = 'code';
    public string $enabledField = 'is_enabled';
    public string $orderField = 'order';

    public function detect(Request $request, bool $isApi = false): ?string
    {
        $param = $this->paramName;

        foreach ([
            fn() => $this->extractValidLang($request->post($param)), // 1. POST
            fn() => $this->extractValidLang($request->query($param)), // 2. GET
            fn() => Auth::check() ? $this->extractValidLang(Auth::user()?->{$this->userAttribute}) : null, // 3. User profile
            fn() => !$isApi ? $this->extractValidLang(Session::get($param)) : null, // 4. Session
            fn() => !$isApi ? $this->extractValidLang(Cookie::get($param)) : null, // 5. Cookies
            fn() => $this->extractValidLang($request->header('Accept-Language')), // 6. Accept-Language header
        ] as $resolver) {
            $lang = $resolver();
            if ($lang) {
                return $this->finalize($lang, $isApi);
            }
        }
        return $this->default; // 7. Default language
    }

    protected function finalize(string $lang, bool $isApi): string
    {
        $param = $this->paramName;

        if (!$isApi) {
            Session::put($param, $lang);
            Cookie::queue(Cookie::make($param, $lang, 525600)); // 1 year
        }

        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            if ($user?->{$this->userAttribute} !== $lang) {
                $user->{$this->userAttribute} = $lang;
                $user->save();
            }
        }

        return $lang;
    }

    protected function extractValidLang($input): ?string
    {
        $prioritized = [];

        if (empty($input)) {
            return null;
        } elseif (is_array($input)) {
            foreach ($input as $lang) {
                $prioritized[$lang] = 1.0;
            }
        } elseif (is_string($input)) {
            foreach (explode(',', $input) as $entry) {
                $parts = explode(';', trim($entry));
                $lang = trim($parts[0]);
                $prioritized[$lang] = isset($parts[1]) && preg_match('/q=([0-9.]+)/', $parts[1], $m)
                    ? (float) $m[1] : 1.0;
            }
        }

        arsort($prioritized);

        $normalized = [];
        foreach (array_keys($prioritized) as $code) {
            $short = strtolower(substr($code, 0, 2));
            if (!isset($normalized[$short])) {
                $normalized[$short] = $prioritized[$code];
            }
        }

        $valid = $this->getAllowedLanguages();

        foreach (array_keys($normalized) as $lang) {
            if (in_array($lang, $valid, true)) {
                return $lang;
            }
        }

        return null;
    }

    protected function getAllowedLanguages(): array
    {
        return Cache::remember("allowed_languages_{$this->tableName}", 3600, function () {
            return DB::table($this->tableName)
                ->where($this->enabledField, true)
                ->orderBy($this->orderField)
                ->pluck($this->codeField)
                ->map(fn($code) => strtolower(substr($code, 0, 2)))
                ->unique()
                ->values()
                ->all();
        });
    }
}
