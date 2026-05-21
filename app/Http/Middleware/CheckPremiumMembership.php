<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPremiumMembership
{
    
    public function handle(Request $request, Closure $next): Response
    {
        $membershipFeature = app(\App\Features\MembershipFeatureInterface::class);
        
        if (!$membershipFeature->canViewChart()) {
            return redirect()->route('membership.index')->with('error', 'Fitur tersebut khusus member Premium. Silakan upgrade membership Anda.');
        }

        return $next($request);
    }
}
