<?php

namespace CulturalInfusion\LaravelSqsFifo\Services;

use Aws\Sqs\SqsClient;
use BadMethodCallException;
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
     * The flag to check whether to use default delay of the Queue on Messages.
     *
     * @var string
     */
    protected string $submit_delay;

    /**
     * The queue name.
     *
     * @var string
     */
    protected string $queue;

    /**
     * The queue name suffix.
     *
     * @var string
     */
    protected $suffix;

    /**
     * Create a new Amazon SQS queue instance.
     *
     * @param  SqsClient  $sqs
     * @param  string  $default
     * @param  string  $prefix
     * @param  string  $suffix
     * @param  string  $message_group_id
     * @param  string  $queue_name_prefix
     * @param  bool    $submit_delay
     * @return void
     */
    public function __construct(
        SqsClient $sqs,
        string $default,
        string $prefix = '',
        string $suffix = '',
        string $message_group_id = null,
        string $queue_name_prefix = '',
        bool $submit_delay = true
    ) {
        parent::__construct($sqs, $default, $prefix);
        $this->message_group_id = $message_group_id;
        $this->queue_name_prefix = $queue_name_prefix;
        $this->submit_delay = $submit_delay;
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
     * Since SQS FIFO does not support delay per message, 
     * this method checks whether to apply delay value of 
     * the queue on the job, or throw an exception otherwise.
     * 
     * @param  \DateTime|int  $delay
     * @param  string  $job
     * @param  mixed  $data
     * @param  string|null  $queue
     *
     * @return mixed
     *
     * @throws BadMethodCallException
     */
    public function later($delay, $job, $data = '', $queue = null): mixed
    {
        if ($this->submit_delay) {
            return $this->push($job, $data, $queue);
        }

        throw new BadMethodCallException('SQS FIFO does not support DelaySeconds per message.');
    }
}
