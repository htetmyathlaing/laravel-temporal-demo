<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Temporal\Client\ClientOptions;
use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowClient;

class TemporalServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(WorkflowClient::class, fn () => WorkflowClient::create(
            ServiceClient::create(config('services.temporal.domain')),
            (new ClientOptions())->withNamespace(config('services.temporal.namespace'))
        ));
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
