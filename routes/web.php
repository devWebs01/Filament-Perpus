<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.welcome');
})->name('welcome');

Route::middleware(['auth'])->group(function () {

    Route::post('/logout', function (Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});

// Route Trigger for Overdue Check
Route::get('/system/check-overdue', function () {
    try {
        Artisan::call('books:check-overdue');

        return response()->json([
            'status' => 'success',
            'message' => 'Overdue check completed.',
            'output' => Artisan::output(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});

// Volt::route('/', 'users.index'); // CONFLICT dengan route welcome di atas
