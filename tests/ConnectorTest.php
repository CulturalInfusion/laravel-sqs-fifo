<?php

namespace CulturalInfusion\LaravelSqsFifo\Tests;

use InvalidArgumentException;
use CulturalInfusion\LaravelSqsFifo\Services\SqsFifoQueue;
use CulturalInfusion\LaravelSqsFifo\Services\SqsFifoConnector;

class ConnectorTest extends TestCase
{
    public function test_sqs_fifo_driver_returns_sqs_fifo_queue()
    {
        $config = $this->app['config']['queue.connections.sqsfifo'];
        $connector = new SqsFifoConnector();

        $connection = $connector->connect($config);

        $this->assertInstanceOf(SqsFifoQueue::class, $connection);
    }
}