<?php

namespace App\Providers;

use App\Services\ProxyService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

/**
 * Class ProxyServiceProvider
 * @package App\Providers
 */
class ProxyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProxyService::class, function (Application $app) {
            $config = $app->make('config')->get('proxy');
            return new ProxyService($config);
        });
    }

}
