<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
// use Twig\Environment;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome', [
            'title' => 'Twig Page',
            'name' => 'Alex'
        ]);
    }
}
