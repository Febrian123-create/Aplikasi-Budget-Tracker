<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckFreeMembership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Jika user bukan bertipe Free (membership_id != 1), arahkan ke dashboard
        if ($user->membership_id != 1) {
            return redirect()->route('dashboard')->with('info', 'Halaman ini hanya dapat diakses oleh pengguna dengan akun Free/Standard.');
        }

        return $next($request);
    }
}
