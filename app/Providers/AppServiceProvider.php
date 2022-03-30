<?php

namespace App\Providers;

use App\Services\ItemService;
use App\Services\SurvivorService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SurvivorService::class, function () {
            return new SurvivorService;
        });

        $this->app->singleton(ItemService::class, function () {
            return new ItemService;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();
    }
}
