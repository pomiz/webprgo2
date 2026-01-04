<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestOnly
{
    /**
     * Handle an incoming request.
     * Only allow guests (non-authenticated users) to access these routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            // If user is logged in, redirect based on role
            $user = auth()->user();

            if ($user->role === 'admin') {
                return redirect('/admin/dashboard');
            } elseif ($user->role === 'user') {
                return redirect('/user');
            }

            // Default fallback
            return redirect('/dashboard');
        }

        return $next($request);
    }
}