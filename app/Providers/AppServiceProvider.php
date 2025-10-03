<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

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
        // Share admin sidebar badge counts (lightweight, cached per request only when admin session present)
        View::composer('admin.partials.sidebar', function ($view) {
            // Default counts
            $counts = [
                'hs_pending' => 0,
                'bm_requests_pending' => 0,
                'donations_pending' => 0,
            ];

            // Only attempt if DB connection ok & admin logged in (avoid overhead on public pages)
            if (session('admin_id')) {
                try {
                    // Health screenings pending (not archived)
                    $counts['hs_pending'] = (int) DB::table('health_screenings')
                        ->where('status', 'pending')
                        ->whereNull('archived_at')
                        ->count();

                    // Breastmilk requests pending (not archived)
                    if (DB::getSchemaBuilder()->hasTable('breastmilk_requests')) {
                        $counts['bm_requests_pending'] = (int) DB::table('breastmilk_requests')
                            ->where('status', 'pending')
                            ->whereNull('archived_at')
                            ->count();
                    }

                    // Pending donations (badge) definition:
                    //   walk_in_requests.status = 'pending'
                    //   PLUS donation_history (home_collection, status='pending', not archived)
                    $walkInPending = 0;
                    if (DB::getSchemaBuilder()->hasTable('walk_in_requests')) {
                        $walkInPending = (int) DB::table('walk_in_requests')
                            ->where('status', 'pending')
                            ->count();
                    }
                    $homeCollectionPending = 0;
                    if (DB::getSchemaBuilder()->hasTable('donation_history')) {
                        $homeCollectionPending = (int) DB::table('donation_history')
                            ->where('donation_type', 'home_collection')
                            ->where('status', 'pending')
                            ->whereNull('archived_at')
                            ->count();
                    }
                    $counts['donations_pending'] = $walkInPending + $homeCollectionPending;
                } catch (\Throwable $e) {
                    // Silently ignore to avoid breaking page if any table missing
                }
            }

            $view->with('adminSidebarCounts', $counts);
        });
    }
}
