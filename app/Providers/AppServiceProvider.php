<?php

namespace App\Providers;

use App\Models\Agency;
use Illuminate\Support\Facades\Auth;
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

    public function boot()
    {
        View::composer('*', function ($view) {

            $user = Auth::user();

            // agencies logic
            if ($user && $user->agency) {
                $currentAgency = $user->agency;
            } else {
                $selectedIds = session('agency_ids', []);
                $currentAgency = !empty($selectedIds) ? Agency::find($selectedIds[0]) : null;
            }

            $agencies = Agency::all();

            $notifications = [];
            $unreadCount = 0;

            if ($user) {
                $notifications = $user->notifications()
                    ->latest()
                    ->take(10)
                    ->get();

                $unreadCount = $user->unreadNotifications()->count();
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
