<?php

use App\Http\Controllers\SocialiteController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('landing'))->name('landing');
Route::get('/docs', fn () => view('docs.swagger'))->name('docs.swagger');
Route::get('/docs/openapi.yaml', fn () => Response::file(public_path('docs/openapi.yaml'), [
    'Content-Type' => 'application/yaml',
]))->name('docs.openapi');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json([
        'message' => 'Email verified successfully.',
    ]);
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->name('socialite.redirect');
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->name('socialite.callback');
