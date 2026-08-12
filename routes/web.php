<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

// Public landing page (guests) — served as a raw file rather than a Blade
// view since it's fully static and its embedded CSS/JS use characters
// (@media, template literals) Blade's compiler would otherwise try to
// parse. Authenticated users land straight in the SPA instead.
Route::get('/', function () {
    if (auth()->check()) {
        return view('app');
    }

    return response()->file(resource_path('landing/index.html'), [
        'Content-Type' => 'text/html; charset=UTF-8',
    ]);
});

Route::get('/register', [RegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [RegisterController::class, 'store'])->middleware('guest');

Route::get('/login', [LoginController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'store'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth');

Route::get('/auth/google', [GoogleController::class, 'redirect'])->middleware('guest')->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->middleware('guest');

Route::middleware('auth')->group(function () {
    // SPA fallback: every non-API, non-asset path renders the same shell
    // so Vue Router (history mode) can take over client-side — otherwise
    // refreshing on e.g. /children would 404 on the Laravel side.
    Route::get('/{any}', function () {
        return view('app');
    })->where('any', '^(?!api|sanctum|storage|login|register|logout).*$');
});
