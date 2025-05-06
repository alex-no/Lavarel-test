<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.welcome', [
            'title' => 'Welcome Page',
            'name' => 'Alex',
            'locale' => app()->getLocale(),
            'loginRouteExists' => Route::has('login'),
            'registerRouteExists' => Route::has('register'),
            'isAuthenticated' => Auth::check(),
            'app' => ['language' => app()->getLocale()],
        ]);
    }
}
