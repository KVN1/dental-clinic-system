<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Force HTTPS in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        \Illuminate\Support\Facades\Blade::directive('money', function ($expression) {
            return "<?php if (session('amounts_visible', false)) { echo '₱' . number_format($expression, 2); } else { echo '<span class=\"blurred-amount\">₱••••••</span>'; } ?>";
        });
    }
}
