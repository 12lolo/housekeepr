<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
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
        // Register model observers
        \App\Models\Booking::observe(\App\Observers\BookingObserver::class);

        // Register event listeners
        Event::listen(
            \App\Events\BookingCreated::class,
            \App\Listeners\CreateCleaningTaskForBooking::class
        );

        Event::listen(
            \App\Events\BookingUpdated::class,
            \App\Listeners\CreateCleaningTaskForBooking::class
        );

        Event::listen(
            \App\Events\BlockingIssueCreated::class,
            \App\Listeners\SendIssueNotification::class
        );

        // Define gates
        Gate::define('manage-hotel', function ($user) {
            return in_array($user->role, ['owner', 'authed-user']);
        });
    }
}
