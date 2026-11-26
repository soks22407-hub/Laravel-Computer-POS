<?php

namespace App\Providers;

use App\Models\Notification;
use App\Models\Order;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Setting;

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
        Schema::defaultStringLength(191);


        View::composer('*', function ($view) {
            $view->with('map', 'https://maps.app.goo.gl/SFQLcT2zEnc92BbR9');

            $unreadCount = Notification::where('is_read', false)->count();

            // Latest 6 notifications
            $notifications = Notification::orderBy('created_at', 'desc')->paginate(6);

            $view->with([
                'unreadCount' => $unreadCount,
                'notifications' => $notifications, 
            ]);
        });

        if (Schema::hasTable('settings')) {
            $globalSettings = \App\Models\Setting::first();
            view()->share('globalSettings', $globalSettings);
        }

        View::composer('*', function ($view) {
            $view->with('setting', Setting::first());
        });
    }
}
