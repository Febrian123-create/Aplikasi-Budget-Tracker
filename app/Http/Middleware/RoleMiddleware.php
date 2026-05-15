<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            abort(403);
        }

        $userRole = Auth::user()->role->role_name;

        if ($userRole !== $role && $userRole !== 'admin') {
            abort(403);
        }

        return $next($request);
    }
}