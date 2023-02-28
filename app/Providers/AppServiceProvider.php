<?php

namespace App\Providers;

use App\Services\Space\SpaceService;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (strpos(env('APP_URL'), 'https') !== false) {
            \URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', 'on');
        }

        if(!file_exists(base_path('config/database_spaces.php'))) {
            SpaceService::prepareAllSpaceConnections();
        }
        SpaceService::prepareAllUploadDirectories();
        Carbon::setLocale(config('app.locale'));
    }
}
