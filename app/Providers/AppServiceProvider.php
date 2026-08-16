<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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

        if (env('APP_ENV') !== 'local' || request()->header('x-forwarded-proto') === 'https') {
            URL::forceScheme('https');
        }

        // Implicitly grant "Super Admin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        try {
            if (Schema::hasTable('settings')) {
                $settings = Setting::pluck('value', 'key')->toArray();
                View::share('settings', $settings);
                View::share('app_name', $settings['app_name'] ?? 'ITAM Enterprise');
                View::share('company_name', $settings['company_name'] ?? 'PT CBA Chemical Industry');
                View::share('company_email', $settings['company_email'] ?? 'itcbachemical23@gmail.com');

                // Dynamic Mail Configuration
                if (($settings['email_notification'] ?? '0') == '1') {
                    config([
                        'mail.mailers.smtp.host' => $settings['smtp_host'] ?? config('mail.mailers.smtp.host'),
                        'mail.mailers.smtp.port' => $settings['smtp_port'] ?? config('mail.mailers.smtp.port'),
                        'mail.mailers.smtp.username' => $settings['smtp_username'] ?? config('mail.mailers.smtp.username'),
                        'mail.mailers.smtp.password' => $settings['smtp_password'] ?? config('mail.mailers.smtp.password'),
                        'mail.from.address' => $settings['company_email'] ?? config('mail.from.address'),
                        'mail.from.name' => $settings['app_name'] ?? config('mail.from.name'),
                    ]);
                }
            } else {
                View::share('app_name', 'ITAM Enterprise');
                View::share('company_name', 'PT CBA Chemical Industry');
                View::share('company_email', 'itcbachemical23@gmail.com');
            }
        } catch (\Exception $e) {
            View::share('app_name', 'ITAM Enterprise');
            View::share('company_name', 'PT CBA Chemical Industry');
            View::share('company_email', 'itcbachemical23@gmail.com');
        }

        // Register Model Event Listeners for Activity Logging
        $loggableModels = [
            \App\Models\Asset::class,
            \App\Models\Employee::class,
            \App\Models\Ticket::class,
            \App\Models\IpAddress::class,
            \App\Models\Vlan::class,
            \App\Models\SoftwareLicense::class,
            \App\Models\PasswordVault::class,
            \App\Models\Vendor::class,
            \App\Models\Location::class,
            \App\Models\Brand::class,
            \App\Models\Category::class,
            \App\Models\Department::class,
            \App\Models\User::class,
        ];

        foreach ($loggableModels as $modelClass) {
            try {
                $modelClass::created(function ($model) {
                    \App\Helpers\ActivityLogger::log($model, 'created');
                });
                $modelClass::updated(function ($model) {
                    \App\Helpers\ActivityLogger::log($model, 'updated');
                });
                $modelClass::deleted(function ($model) {
                    \App\Helpers\ActivityLogger::log($model, 'deleted');
                });
            } catch (\Exception $e) {
                logger()->error("Failed to register activity observers for {$modelClass}: " . $e->getMessage());
            }
        }

        try {
            \App\Models\AssetAssignment::created(function ($model) {
                \App\Helpers\ActivityLogger::log($model, $model->status === 'Assigned' ? 'assigned' : 'returned');
            });
            \App\Models\AssetAssignment::updated(function ($model) {
                if ($model->isDirty('status')) {
                    \App\Helpers\ActivityLogger::log($model, $model->status === 'Assigned' ? 'assigned' : 'returned');
                }
            });

            \App\Models\Maintenance::created(function ($model) {
                \App\Helpers\ActivityLogger::log($model, 'maintenance');
            });
        } catch (\Exception $e) {
            logger()->error("Failed to register special model observers: " . $e->getMessage());
        }
    }
}
