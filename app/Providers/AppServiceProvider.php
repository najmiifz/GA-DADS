<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Asset;
use App\Observers\AssetObserver;
use App\Models\AssetPage;
use App\Models\User;

if (! function_exists('normalize_string')) {
    function normalize_string($v) {
        if (is_null($v)) return $v;
        $s = (string) $v;
        // replace NBSP
        $s = str_replace("\xC2\xA0", ' ', $s);
        // remove control/invisible chars
        $s = preg_replace('/\p{C}+/u', '', $s);
        // collapse whitespace
        $s = preg_replace('/\s+/u', ' ', $s);
        $s = trim($s);
        if (class_exists('\\Normalizer')) {
            $s = \Normalizer::normalize($s, \Normalizer::FORM_KC) ?: $s;
        }
        return $s;
    }
}

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
        // Share DB-driven filter options globally for all views
        try {
            $shared = [];

            // helper to fetch distinct values and normalize + unique case-insensitive
            $fetchDistinct = function($col) {
                $vals = Asset::select($col)->distinct()->pluck($col)->filter()->map(function($v){ return normalize_string($v); })->filter();
                // preserve 'Kendaraan' and 'Splicer' values as they are (do not alter casing removal)
                // unique case-insensitive for rest
                $unique = [];
                foreach ($vals as $v) {
                    $lower = mb_strtolower($v);
                    if (!array_key_exists($lower, $unique)) {
                        $unique[$lower] = $v;
                    }
                }
                $arr = array_values($unique);
                sort($arr);
                return $arr;
            };

            $shared['filter_tipe'] = $fetchDistinct('tipe');
            $shared['filter_project'] = $fetchDistinct('project');
            $shared['filter_lokasi'] = $fetchDistinct('lokasi');
            $shared['filter_jenis_aset'] = $fetchDistinct('jenis_aset');
            $shared['filter_pic'] = $fetchDistinct('pic');

            View::share($shared);
        } catch (\Throwable $e) {
            // fail silently in boot to avoid breaking artisan commands
        }

        // Central composer for asset-related views to provide users filtered for PIC selects
        View::composer(['assets.create', 'assets.edit', 'assets.vehicles', 'assets.*'], function ($view) {
            try {
                $usersForPic = User::query()
                    ->get()
                    ->filter(function ($u) {
                        $name = strtolower(trim((string)($u->name ?? '')));
                        $role = strtolower(trim((string)($u->role ?? '')));
                        // Exclude both super-admin and admin accounts from PIC lists
                        if (in_array($role, ['super-admin', 'admin'])) return false;
                        if (in_array($name, ['super-admin', 'admin'])) return false;
                        return true;
                    })
                    ->values();

                $view->with('usersForPic', $usersForPic);
            } catch (\Throwable $_) {
                $view->with('usersForPic', collect([]));
            }
        });

        // Register model observers
        try {
            Asset::observe(AssetObserver::class);
        } catch (\Throwable $_) {
            // ignore in environments where the model/table isn't available yet
        }

        // Share custom asset pages with all views, specifically for the sidebar
        try {
            if (Schema::hasTable('asset_pages')) {
                View::composer('layouts.app', function ($view) {
                    // Hanya sertakan halaman unik berdasarkan slug menggunakan collection unique
                    $pages = AssetPage::all()
                        ->unique('slug')
                        ->values();
                    $view->with('customPages', $pages);
                });
            }
        } catch (\Throwable $e) {
            // ignore if database or table not available during boot
        }
    }
}
