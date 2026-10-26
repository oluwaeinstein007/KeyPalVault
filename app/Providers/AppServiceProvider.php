<?php

namespace App\Providers;

use Cloudinary\Cloudinary;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Same configuration cloudinary-labs/cloudinary-laravel used: the CLOUDINARY_URL.
        $this->app->singleton(Cloudinary::class, function ($app) {
            return new Cloudinary($app['config']->get('cloudinary.cloud_url'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
