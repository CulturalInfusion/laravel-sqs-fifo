<?php

namespace CulturalInfusion\LaravelSqsFifo\Tests;

use Aws\Result;
use Aws\Sqs\SqsClient;
use BadMethodCallException;
use CulturalInfusion\LaravelSqsFifo\Services\SqsFifoQueue;
use Illuminate\Support\Collection;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Queue\CallQueuedHandler;
use Illuminate\Notifications\SendQueuedNotifications;
use InvalidArgumentException;
use Mockery;

class QueueTest extends TestCase
{
    public function test_queue_sends_message_group_id()
    {
        $group = 'default';
        $job = 'test';
        $closure = function ($message) use ($group) {
            if ($message['MessageGroupId'] != $group) {
                return false;
            }

            return true;
        };

        $messageId = 1234;

        $result = new Result(['MessageId' => $messageId]);
        $client = Mockery::mock(SqsClient::class);
        $client->shouldReceive('sendMessage')->with(Mockery::on($closure))->andReturn($result);

        $queue = new SqsFifoQueue($client, '', '', '', $group, '');
        $queue->setContainer($this->app);

        $this->assertEquals($queue->pushRaw($job), $messageId);
    }
}