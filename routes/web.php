<?php

use Illuminate\Support\Facades\Route;

// Public landing page (guests) — served as a raw file rather than a Blade
// view since it's fully static and its embedded CSS/JS use characters
// (@media, template literals) Blade's compiler would otherwise try to
// parse. Authenticated users will land in the SPA once auth exists.
Route::get('/', function () {
    if (auth()->check()) {
        return view('app');
    }

    return response()->file(resource_path('landing/index.html'), [
        'Content-Type' => 'text/html; charset=UTF-8',
    ]);
});

Route::middleware('auth')->group(function () {
    // SPA fallback: every non-API, non-asset path renders the same shell
    // so Vue Router (history mode) can take over client-side.
    Route::get('/{any}', function () {
        return view('app');
    })->where('any', '^(?!api|sanctum|storage).*$');
});
