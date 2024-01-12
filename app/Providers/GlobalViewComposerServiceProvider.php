<?php

namespace App\Providers;

use App\Models\Story;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Stevebauman\Location\Facades\Location;


class GlobalViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function ($view) {
            $today = Carbon::now(); 
            $storyData = Story::whereDate('created_at', $today->toDateString())
                ->latest()
                ->take('10')
                ->get();        
        
            $view->with('storyData', $storyData);
        });
    }
}
