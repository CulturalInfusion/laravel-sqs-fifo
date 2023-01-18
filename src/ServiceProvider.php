<?php

namespace CulturalInfusion\LaravelSqsFifo;

use CulturalInfusion\LaravelSqsFifo\Services\SqsFifoConnector;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Foundation\Console\AboutCommand;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->afterResolving('queue', function ($manager) {
            $manager->addConnector('sqsfifo', function () {
                return new SqsFifoConnector();
            });
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        AboutCommand::add('Laravel SQS FIFO', fn () => [
            'Support' => 'Queue driver for SQS FIFO, with flexible endpoint structure'
        ]);
    }
}