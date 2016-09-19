<?php

/*
 * This file is part of DataTracker.
 *
 * (c) Jeromy Llerena Arroyo <jeromyllerna@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace DataTracker\Providers;

use Illuminate\Support\ServiceProvider;
use DataTracker\Commands\TraceRequest;

class TrackerServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('jwt.php'),
        ], 'config');


        $this->commands('tracker.trace.data');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // register providers
        $this->registerTrackerCommand();

    }

    /**
     * Register the Artisan command.
     */
    protected function registerTrackerCommand()
    {
        $this->app['tracker.trace.data'] = $this->app->share(function () {
            return new TraceRequest();
        });
    }

}
