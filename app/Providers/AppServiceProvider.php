<?php

namespace App\Providers;

use App\Models\Agency;
use App\Models\Lead;
use App\Models\LeadPricing;
use App\Observers\LeadObserver;
use App\Observers\LeadPricingObserver;
use App\Rules\NoWhitespace;
use App\Services\AttendanceService;
use App\Support\LeadStages;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Lead Staging stages, loaded once per request (see LeadStages).
        $this->app->scoped(LeadStages::class);
    }

    public function boot()
    {
        // Single password policy for login, password reset and the
        // Users Add/Edit forms (rules use Password::defaults()) - keep
        // the client-side checks on those pages in step with it.
        Password::defaults(fn () => Password::min(8)->rules([new NoWhitespace]));

        // Logging in is the attendance Time In - covers password, OTP
        // and "remember me" logins alike.
        Event::listen(Login::class, function (Login $event) {
            if ($event->user instanceof \App\Models\User) {
                app(AttendanceService::class)->clockIn($event->user);
            }
        });

        Lead::observe(LeadObserver::class);
        LeadPricing::observe(LeadPricingObserver::class);

        View::composer('*', function ($view) {

            $user = Auth::user();

            // Memoized on the request like the notification data below -
            // this composer runs for every view and partial rendered.
            $agencyData = request()->attributes->get('agency_data');

            if (!$agencyData) {
                if ($user && $user->agency) {
                    $currentAgency = $user->agency;
                } else {
                    $selectedIds = session('agency_ids', []);
                    $currentAgency = !empty($selectedIds) ? Agency::find($selectedIds[0]) : null;
                }

                $agencyData = ['agencies' => Agency::all(), 'currentAgency' => $currentAgency];

                request()->attributes->set('agency_data', $agencyData);
            }

            ['agencies' => $agencies, 'currentAgency' => $currentAgency] = $agencyData;

            $notifications = [];
            $unreadCount = 0;

            if ($user) {
                // Memoized on the request - this composer runs for every
                // view/partial rendered, and the bell needs the same two
                // notification queries each time. (Stored on the request
                // itself so it can never leak across requests or users.)
                $data = request()->attributes->get('notification_data');

                if (!$data) {
                    $data = [
                        'notifications' => $user->notifications()->latest()->take(10)->get(),
                        'unread' => $user->unreadNotifications()->count(),
                    ];

                    request()->attributes->set('notification_data', $data);
                }

                $notifications = $data['notifications'];
                $unreadCount = $data['unread'];
            }

            $view->with([
                'agencies' => $agencies,
                'currentAgency' => $currentAgency,
                'notifications' => $notifications,
                'unreadCount' => $unreadCount
            ]);
        });
    }
}
