<?php

namespace CulturalInfusion\LaravelSqsFifo\Tests;

use CulturalInfusion\LaravelSqsFifo\ServiceProvider;
use CulturalInfusion\LaravelSqsFifo\Services\SqsFifoConnector;
use Illuminate\Queue\Capsule\Manager as Capsule;
use Orchestra\Testbench\TestCase as BaseTestCase;
use ReflectionMethod;
use ReflectionProperty;

class TestCase extends BaseTestCase
{
    /**
     * The Illuminate Container used by the queue.
     *
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * The Queue Capsule instance for the tests.
     *
     * @var \Illuminate\Queue\Capsule\Manager
     */
    protected $queue;

    /**
     * Initial setup for all tests.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpCapsule();
        $this->setUpQueueConnection();
        $this->registerServiceProvider();
    }

    /**
     * Setup the Queue Capsule.
     *
     * @return void
     */
    public function setUpCapsule()
    {
        $queue = new Capsule();
        $queue->setAsGlobal();

        $this->queue = $queue;
        $this->app = $queue->getContainer();

        $this->app->instance('queue', $queue->getQueueManager());
    }

    /**
     * Register the service provider for the package.
     *
     * @return void
     */
    public function registerServiceProvider()
    {
        $provider = new ServiceProvider($this->app);

        $provider->register();
    }

    /**
     * Setup the database connection.
     *
     * @return void
     */
    public function setUpQueueConnection()
    {
        $queue = $this->queue;
        
        // Default Connection
        $queue->addConnection([
            'driver' => 'sync',
        ]);

        $connection = 'sqsfifo';
        
        $queue->addConnection([
            'driver' => 'sqsfifo',
            'key' => '',
            'secret' => '',
            'credentials' => false,
            'endpoint' => 'http://app-docker:9324',
            'prefix' => '1234',
            'suffix' => '',
            'queue' => 'default',
            'queue_name_prefix' => '',
            'message_group_id' => '1234/default',
            'region' => 'ap-southeast-2',
        ], $connection);

        $queue->getQueueManager()->addConnector($connection, function () {
            return new SqsFifoConnector($this->app);
        });
    }

    /**
     * Use reflection to call a restricted (private/protected) method on an object.
     *
     * @param  object  $object
     * @param  string  $method
     * @param  array  $args
     *
     * @return mixed
     */
    protected function callRestrictedMethod($object, $method, array $args = [])
    {
        $reflectionMethod = new ReflectionMethod($object, $method);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod->invokeArgs($object, $args);
    }

    /**
     * Use reflection to get the value of a restricted (private/protected)
     * property on an object.
     *
     * @param  object  $object
     * @param  string  $property
     *
     * @return mixed
     */
    protected function getRestrictedValue($object, $property)
    {
        $reflectionProperty = new ReflectionProperty($object, $property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }

    /**
     * Use reflection to set the value of a restricted (private/protected)
     * property on an object.
     *
     * @param  object  $object
     * @param  string  $property
     * @param  mixed  $value
     *
     * @return void
     */
    protected function setRestrictedValue($object, $property, $value)
    {
        $reflectionProperty = new ReflectionProperty($object, $property);
        $reflectionProperty->setAccessible(true);

        if ($reflectionProperty->isStatic()) {
            $reflectionProperty->setValue($value);
        } else {
            $reflectionProperty->setValue($object, $value);
        }
    }
}
