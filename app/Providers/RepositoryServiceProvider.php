<?php

namespace App\Providers;

use App\Interfaces\BaseEloquentInterface;
use App\Interfaces\CartRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\CouponRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\SoftDeletingRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\VendorRepositoryInterface;
use App\Repositories\BaseEloquentRepository;
use App\Repositories\CartRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\CouponRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SoftDeletingRepository;
use App\Repositories\UserRepository;
use App\Repositories\VendorRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(BaseEloquentInterface::class, BaseEloquentRepository::class);
        $this->app->bind(SoftDeletingRepositoryInterface::class, SoftDeletingRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(CouponRepositoryInterface::class, CouponRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(VendorRepositoryInterface::class, VendorRepository::class);
        $this->app->bind(CartRepositoryInterface::class, CartRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
