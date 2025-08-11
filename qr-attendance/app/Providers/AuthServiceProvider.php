<?php

namespace App\Providers;

use App\Models\TutoringSession;
use App\Policies\TutoringSessionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        TutoringSession::class => TutoringSessionPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}