<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;

class RedirectIfNotFilamentAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $auth = Filament::auth();
        $user = $auth->user();
        $panel = Filament::getCurrentPanel();

        // Belum login → biarkan Filament handle
        if (! $auth->check()) {
            return redirect()->to(Filament::getLoginUrl());
        }

        // Sudah login tapi tidak boleh akses panel
        if (! $user || ! $user->canAccessPanel($panel)) {
            // auth()->logout();

            return redirect('/')
                ->with('message', 'Anda tidak memiliki akses ke halaman admin.');
        }

        return $next($request);
    }
}
