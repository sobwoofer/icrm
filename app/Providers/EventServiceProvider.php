<?php

namespace App\Providers;

use App\Events\FreshItemsFound;
use App\Events\States\AddFilter;
use App\Events\FirstFilterCrawled;
use App\Events\States\Hunting;
use App\Events\States\Info;
use App\Events\States\RemoveFilter;
use App\Events\States\RunFilter;
use App\Events\States\ShowFilters;
use App\Events\States\Start;
use App\Events\States\StopFilter;
use App\Listeners\FirstFilterCrawledListener;
use App\Listeners\FreshItemsFoundListener;
use App\Listeners\States\AddFilterListener;
use App\Listeners\States\HuntingListener;
use App\Listeners\States\InfoListener;
use App\Listeners\States\RemoveFilterListener;
use App\Listeners\States\RunFilterListener;
use App\Listeners\States\ShowFiltersListener;
use App\Listeners\States\StartListener;
use App\Listeners\States\StopFilterListener;
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
