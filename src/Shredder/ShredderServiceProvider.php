<?php

namespace Juksy\Shredder;


use Illuminate\Support\ServiceProvider;

class ShredderServiceProvider extends ServiceProvider
{
    protected $app_id;
    protected $app_secret;

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
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Set object
        $me = $this;

        $this->app->singleton('shredder', function () use ($me)
        {
            return new ShredderHandler($me->app_id, $me->$app_secret);
        });
    }
}
