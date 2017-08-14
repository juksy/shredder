<?php

namespace Juksy\Shredder;

use Illuminate\Support\ServiceProvider;

class ShredderServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/shredder.php', 'shredder'
        );

        // Publish a config file
        $this->publishes([
            __DIR__ . '/../../config/shredder.php' => config_path('shredder.php')
        ], 'config');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('shredder', function ($app)
        {
            $config = $app['config']->get('shredder');

            // Using session by Laravel.
            $config['persistent_data_handler'] = new LaravelPersistentDataHandler($app['session.store']);

            return new ShredderHandler($app, $config);
        });
    }
}
