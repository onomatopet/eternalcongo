<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }

        $user = Auth::user();
        if (!$user->role_id || $user->role_id !== 1) {
            return redirect()->route('dashboard')->with('error', 'Accès refusé. Vous devez être administrateur.');
        }

        return $next($request);
    }
}