<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('pages.welcome');
})->name('welcome');

Route::middleware(['auth'])->group(function () {

    Route::post('/logout', function (Request $request) {
        Auth::guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});

Volt::route('/', 'users.index');
