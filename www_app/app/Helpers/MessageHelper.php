<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

class MessageHelper
{
     /**
     * Returns an array of translated messages for the given keys and locale.
     *
     * @param string[] $keys   Array of message keys, e.g. ['email_verified', 'invalid_credentials']
     * @param string|null $locale Language code (e.g. 'en', 'uk'); if null, uses App::getLocale()
     * @return array Associative array like ['key' => 'translated text']
     *
     * @throws InvalidArgumentException If the language file does not exist
     */
    public static function getMessages(array $keys, ?string $locale = null): array
    {
        $locale = $locale ?? App::getLocale();

        $langFilePath = resource_path("lang/{$locale}/messages.php");

        if (!File::exists($langFilePath)) {
            throw new InvalidArgumentException("Language file [{$locale}] does not exist.");
        }

        $fallbackLocale = Config::get('messages.fallback_locale', 'uk');
        $cacheTtl = Config::get('messages.cache_lifetime', 600); // fallback 10 minutes

        
        return collect($keys)
            ->mapWithKeys(function ($key) use ($locale, $fallbackLocale, $cacheTtl) {
                // Create a cache key based on locale and message key
                $cacheKey = "message.{$locale}.{$key}";

                return [
                    $key => Cache::remember($cacheKey, $cacheTtl, function () use ($key, $locale, $fallbackLocale) {
                    // $key => Cache::rememberForever($cacheKey, function () use ($key, $locale, $fallbackLocale) {
                        $message = Lang::get("messages.{$key}", [], $locale);

                        // If the message equals the key, try fallback
                        if ($message === "messages.{$key}" && $locale !== $fallbackLocale) {
                            $message = Lang::get("messages.{$key}", [], $fallbackLocale);
                        }

                        // If still not found, return the key itself
                        return $message === "messages.{$key}" ? $key : $message;
                    })
                ];
            })
            ->all();
    }
}