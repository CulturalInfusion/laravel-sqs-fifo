<?php

namespace CulturalInfusion\LaravelSqsFifo\Services;

use Aws\Sqs\SqsClient;
use Illuminate\Support\Arr;

class SqsFifoConnector extends \Illuminate\Queue\Connectors\SqsConnector
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \Illuminate\Queue\SqsQueue
     */
    public function connect($config)
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
            $config['queue_name_prefix']
        );
    }
}
