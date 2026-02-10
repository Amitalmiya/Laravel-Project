<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Mhetreramesh\Flysystem\BackblazeAdapter;
use BackblazeB2\Client;

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
        Storage::extend('b2', function ($app, $config) {
            $client = new Client($config['accountId'], $config['applicationKey']);
            
            $adapter = new BackblazeAdapter(
                $client,
                $config['bucketName'],
                isset($config['bucketId']) ? $config['bucketId'] : null
            );
            
            return new Filesystem($adapter);
        });
    }
}