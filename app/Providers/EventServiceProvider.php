<?php

namespace App\Providers;

use App\Events\CreatedProduct;
use App\Events\ProductCrawled;
use App\Listeners\CreatedProductListener;
use App\Listeners\ProductCrawledListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ProductCrawled::class => [
            ProductCrawledListener::class,
        ],
        CreatedProduct::class => [
            CreatedProductListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
