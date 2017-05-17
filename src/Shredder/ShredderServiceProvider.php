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
            $config = [
                'app_id' => $app['config']->get('shredder.app_id'),
                'app_secret' => $app['config']->get('shredder.app_secret'),
                'default_graph_version' => $app['config']->get('shredder.default_graph_version'),
            ];

            $config['persistent_data_handler'] = new LaravelPersistentDataHandler($app['session.store']);

            return new ShredderHandler($config);
        });
    }
}
