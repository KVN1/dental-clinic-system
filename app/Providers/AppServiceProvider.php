<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;

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

        // Custom @money directive - respects the privacy blur toggle
        Blade::directive('money', function ($expression) {
            return "<?php if (session('amounts_visible', false)) { echo '₱' . number_format($expression, 2); } else { echo '<span class=\"blurred-amount\">₱••••••</span>'; } ?>";
        });

        // Share reminders count with the sidebar on every authenticated page
        View::composer('layouts.clinic-layout', function ($view) {
            if (auth()->check()) {
                $weekCount = \App\Models\Appointment::whereBetween('appointment_date', [today(), today()->addDays(7)])
                    ->whereNotIn('status', ['cancelled'])
                    ->count();

                $overdueCount = \App\Models\Patient::where('balance', '>', 0)->count();

                $staleRxCount = \App\Models\Prescription::where('status', 'active')
                    ->where('date_issued', '<=', now()->subDays(30))
                    ->count();

                $view->with('remindersCount', $weekCount + $overdueCount + $staleRxCount);
            } else {
                $view->with('remindersCount', 0);
            }
        });
    }
}