<?php

namespace CulturalInfusion\LaravelSqsFifo\Services;

use Aws\Sqs\SqsClient;
use Illuminate\Support\Arr;
use Illuminate\Queue\Connectors\SqsConnector;

class SqsFifoConnector extends SqsConnector
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return SqsFifoQueue
     */
    public function connect(array $config): SqsFifoQueue
    {
        $config = $this->getDefaultConfiguration($config);

        if ($config['key'] && $config['secret']) {
            $config['credentials'] = Arr::only($config, ['key', 'secret']);
        }

        return new SqsFifoQueue(
            new SqsClient($config),
            $config['queue'],
            $config['prefix'] ?? '',
            $config['suffix'] ?? '',
            $config['message_group_id'],
            $config['queue_name_prefix'],
            $config['submit_delay'] ?? true,
        );
    }
}
