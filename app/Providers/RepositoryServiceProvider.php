<?php

namespace App\Providers;

use App\Interfaces\BaseEloquentInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Repositories\BaseEloquentRepository;
use App\Repositories\CategoryRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(BaseEloquentInterface::class, BaseEloquentRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
