<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use App\Models\Language;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Загружаем список доступных языков из БД
        $enabledLanguages = Language::where('is_enabled', true)->pluck('code')->toArray();

        // Приоритетные источники языка
        $possibleLocales = [
            $request->get('lang'),
            Cookie::get('lang'),
            Session::get('lang'),
            $request->getPreferredLanguage($enabledLanguages),
            config('app.locale'),
        ];

        // Выбираем первый доступный язык
        $locale = collect($possibleLocales)->first(fn($lang) => in_array($lang, $enabledLanguages));

        // Устанавливаем локаль
        App::setLocale($locale);

        // Сохраняем язык в сессии
        Session::put('lang', $locale);

        // Сохраняем язык в куки на 1 год
        $response = $next($request);
        return $response->withCookie(cookie('lang', $locale, 525600)); // 525600 минут = 1 год
    }
}
