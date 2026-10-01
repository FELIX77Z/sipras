<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer(['layouts.admin', 'layouts.guru', 'layouts.user', 'notifications.index', 'components.notifications-dropdown'], function (ViewInstance $view): void {
            $user = Auth::user();

            $view->with([
                'recentNotifications' => $user?->notifications()->latest()->take(5)->get() ?? collect(),
                'unreadNotificationCount' => $user?->notifications()->whereNull('dibaca_pada')->count() ?? 0,
            ]);
        });
    }
}
