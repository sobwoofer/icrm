<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use App\Services\ImebliClient;

/**
 * Class ImebliClientProvider
 * @package App\Providers
 */
class ImebliClientProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ImebliClient::class, function (Application $app) {
            $config = $app->make('config')->get('imebli-api');
            return new ImebliClient($config['host'], $config['token']);
        });
    }
}
