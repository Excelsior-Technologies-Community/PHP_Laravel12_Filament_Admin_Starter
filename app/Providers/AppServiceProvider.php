<?php

namespace App\Providers;

use App\Models\LoginActivity;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(Login::class, function (Login $event): void {
            $user = $event->user;

            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);

            LoginActivity::create([
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'login_at' => now(),
            ]);
        });
    }
}