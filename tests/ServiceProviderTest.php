<?php

namespace CulturalInfusion\LaravelSqsFifo\Tests;

use CulturalInfusion\LaravelSqsFifo\Services\SqsFifoConnector;
use Illuminate\Container\Container;
use Illuminate\Queue\QueueServiceProvider;

class ServiceProviderTest extends TestCase
{
    public function test_sqs_fifo_driver_is_registered_with_capsule()
    {
        $connector = $this->callRestrictedMethod($this->queue->getQueueManager(), 'getConnector', ['sqsfifo']);
        $this->assertInstanceOf(SqsFifoConnector::class, $connector);
    }
}