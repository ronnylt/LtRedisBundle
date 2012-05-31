<?php

namespace Lt\Bundle\RedisBundle\Monolog\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Lt\Bundle\RedisBundle\Connection\RedisClient;

/**
 * Monlog processing handler that stores the record in a Redis list.
 */
class RedisHandler extends AbstractProcessingHandler
{
    /**
     * @var RedisClient
     */
    protected $redis;

    /**
     * Records buffer
     * @var array
     */
    protected $records = array();

    /**
     * Redis key where records are stored.
     * @var string
     */
    protected $key;

    /**
     * @param RedisClient $redis
     */
    public function setRedis($redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $records = &$this->buffer;
        $key = &$this->key;
        $this->redis->multiExec(
            function($multi) use ($key, $records) {
                foreach ($records as $record) {
                    $multi->rpush($key, $record);
                }
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        $this->records[] = (string) $record['formatted'];
    }
}
