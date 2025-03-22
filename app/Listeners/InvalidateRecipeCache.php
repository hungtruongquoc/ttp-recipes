<?php

namespace App\Listeners;

use App\Events\CacheDataChanged;
use Illuminate\Support\Facades\Cache;

class InvalidateRecipeCache
{
    /**
     * Handle the event.
     *
     * @param  CacheDataChanged  $event
     * @return void
     */
    public function handle(CacheDataChanged $event): void
    {
        Cache::forget($event->cacheKey);
    }
}
