<?php

namespace CulturalInfusion\LaravelSqsFifo\Tests;

use CulturalInfusion\LaravelSqsFifo\Services\{SqsFifoConnector, SqsFifoQueue};

class ConnectorTest extends TestCase
{
    public function test_sqs_fifo_driver_returns_sqs_fifo_queue()
    {
        $config = $this->container['config']['queue.connections.sqsfifo'];
        $connector = new SqsFifoConnector();

        $connection = $connector->connect($config);

        $this->assertInstanceOf(SqsFifoQueue::class, $connection);
    }
}