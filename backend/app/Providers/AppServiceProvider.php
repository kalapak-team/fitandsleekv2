<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\Product;
use App\Observers\OrderObserver;
use App\Observers\ShipmentObserver;
use App\Observers\ProductObserver;
use Illuminate\Auth\Notifications\ResetPassword;

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
        // Route model binding for customer parameter
        Route::model('customer', User::class);

        // Ensure generated URLs use HTTPS in production to avoid mixed-content warnings
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        ResetPassword::createUrlUsing(function (User $user, string $token): string {
            $frontendUrl = rtrim(config('app.frontend_url'), '/');

            return $frontendUrl . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);
        });

        Order::observe(OrderObserver::class);
        Shipment::observe(ShipmentObserver::class);
        Product::observe(ProductObserver::class);
    }
}

