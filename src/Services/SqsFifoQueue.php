<?php

namespace CulturalInfusion\LaravelSqsFifo\Services;

use Illuminate\Queue\SqsQueue;

class SqsFifoQueue extends SqsQueue
{
    /**
     * The message group ID.
     *
     * @var string
     */
    protected $message_group_id;

    /**
     * The queue name suffix.
     *
     * @var string
     */
    protected $queue_name_prefix;

    /**
     * The queue name suffix.
     *
     * @var string
     */
    protected $queue;

    /**
     * The queue name suffix.
     *
     * @var string
     */
    private $suffix;

    /**
     * Create a new Amazon SQS queue instance.
     *
     * @param  \Aws\Sqs\SqsClient  $sqs
     * @param  string  $default
     * @param  string  $prefix
     * @param  string  $suffix
     * @param  bool  $dispatchAfterCommit
     * @return void
     */
    public function __construct(
        $sqs,
        $default,
        $prefix = '',
        $suffix = '',
        $message_group_id = null,
        $queue_name_prefix = ''
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
    public function getQueue($queue)
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
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, $options = [])
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
     * @throw  \Exception
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        throw new \Exception("Cannot support DelaySeconds for FIFO queues.");
    }
}
