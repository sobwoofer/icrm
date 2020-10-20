<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use App\Services\OpencartClient;

/**
 * Class OpencartClientProvider
 * @package App\Providers
 */
class OpencartClientProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(OpencartClient::class, function (Application $app) {
            $config = $app->make('config')->get('imebli-api');
            return new OpencartClient($config['host'], $config['token']);
        });
    }
}
