<?php

namespace CulturalInfusion\LaravelSqsFifo\Services;

use Aws\Sqs\SqsClient;
use Exception;
use Illuminate\Queue\SqsQueue;

class SqsFifoQueue extends SqsQueue
{
    /**
     * The message group ID.
     *
     * @var string
     */
    protected string $message_group_id;

    /**
     * The queue name suffix.
     *
     * @var string
     */
    protected string $queue_name_prefix;

    /**
     * The queue name suffix.
     *
     * @var string
     */
    protected string $queue;

    /**
     * The queue name suffix.
     *
     * @var string
     */
    private string $suffix;

    /**
     * Create a new Amazon SQS queue instance.
     *
     * @param  SqsClient  $sqs
     * @param  string  $default
     * @param  string  $prefix
     * @param  string  $suffix
     * @param  string  $message_group_id
     * @return void
     */
    public function __construct(
        SqsClient $sqs,
        string $default,
        string $prefix = '',
        string $suffix = '',
        string $message_group_id = null,
        string $queue_name_prefix = ''
    ) {
        parent::__construct($sqs, $default, $prefix);
        $this->message_group_id = $message_group_id;
        $this->queue_name_prefix = $queue_name_prefix;
        $this->suffix = $suffix;
    }

    /**
     * Get the queue or return the default.
     *
     * @param  string|null  $queue
     * @return string
     */
    public function getQueue($queue): string
    {
        $queue = $queue ?: $this->default;
        $queue = sprintf('%s%s%s', $this->queue_name_prefix, $queue, '.fifo');
        return filter_var($queue, FILTER_VALIDATE_URL) === false ? $this->suffixQueue($queue, $this->suffix) : $queue;
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string  $payload
     * @param  string|null  $queue
     * @param  array  $options
     * @return string|null
     */
    public function pushRaw($payload, $queue = null, $options = []): string|null
    {
        $response = $this->sqs->sendMessage([
            'QueueUrl' => $this->getQueue($queue),
            'MessageBody' => $payload,
            'MessageGroupId' => $this->message_group_id ?? uniqid(),
            'MessageDeduplicationId' => uniqid(),
        ]);

        return $response->get('MessageId');
    }

    /**
     * SQS FIFO does not support DelaySeconds currently.
     *
     * @throw  Exception
     */
    public function later($delay, $job, $data = '', $queue = null): Exception
    {
        throw new Exception("Cannot support DelaySeconds for FIFO queues.");
    }
}
