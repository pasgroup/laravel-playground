<?php

namespace App\Providers;

use App\Application\Task\Repository\TaskRepositoryInterface;
use App\Domain\Task\Policy\TaskTransition;
use App\Domain\Task\Policy\TaskTransitionPolicyInterface;
use App\Infrastructure\Persistence\Task\EloquentTaskRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TaskRepositoryInterface::class, EloquentTaskRepository::class);
        $this->app->bind(TaskTransitionPolicyInterface::class, TaskTransition::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
