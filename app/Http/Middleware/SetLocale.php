<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Jika ada session 'locale', gunakan itu. Jika tidak, gunakan 'id' sebagai default.
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            App::setLocale('id'); // Default bahasa Indonesia
        }

        return $next($request);
    }
}