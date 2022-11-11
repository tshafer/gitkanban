<?php

use App\Http\Controllers\SocialAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', fn () => view('welcome'));

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::get('auth/{myprovider}/logout', [SocialAuthController::class, 'unlinkSocialProvider'])->name('auth.social.logout');
    Route::get('auth/{myprovider}', [SocialAuthController::class, 'redirectToProvider'])->name('auth.social');
    Route::get('auth/{myprovider}/callback', [SocialAuthController::class, 'handleProviderCallback'])->name('auth.social.callback');

    Route::get('/source-providers', \App\Http\Livewire\SourceProviders\Index::class)->name('source-providers');
});
