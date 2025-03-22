<?php

namespace App\Providers;

use App\Events\CacheDataChanged;
use App\Listeners\InvalidateRecipeCache;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        CacheDataChanged::class => [
            InvalidateRecipeCache::class,
        ],
    ];
}
