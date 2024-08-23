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
        $this->app->bind(BaseEloquentInterface::class, BaseEloquentRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
