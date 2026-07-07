<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event; // <-- Add this
use Illuminate\Auth\Events\Login; // <-- Add this
use App\Listeners\EnforceSingleSession; // <-- Add this

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Listen for ANY login on your application and trigger our single-session rule
        Event::listen(
            Login::class,
            EnforceSingleSession::class,
        );
    }
}
