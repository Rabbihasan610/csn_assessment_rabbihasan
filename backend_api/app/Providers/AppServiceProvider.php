<?php

namespace App\Providers;

use App\Services\Contracts\ProductServiceInterface;
use App\Services\Contracts\ShopProductServiceInterface;
use App\Services\Contracts\ShopServiceInterface;
use App\Services\ProductService;
use App\Services\ShopProductService;
use App\Services\ShopService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(ShopServiceInterface::class, ShopService::class);
        $this->app->bind(ShopProductServiceInterface::class, ShopProductService::class);
    }

    public function boot(): void
    {
    }
}
