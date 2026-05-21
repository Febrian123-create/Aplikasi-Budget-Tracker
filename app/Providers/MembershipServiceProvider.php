<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Features\MembershipFeatureInterface;
use App\Features\MembershipFactory;
use App\Features\StandardMembership;

class MembershipServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        $this->app->bind(MembershipFeatureInterface::class, function ($app) {
            $user = auth()->user();
            if ($user) {
                return MembershipFactory::create($user);
            }
            return new StandardMembership();
        });
    }

    
    public function boot(): void
    {
        
    }
}
