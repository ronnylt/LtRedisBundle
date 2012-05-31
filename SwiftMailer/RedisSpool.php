<?php

namespace Lt\Bundle\RedisBundle\SwiftMailer;

use Lt\Bundle\RedisBundle\Connection\RedisClient;

/**
 * Stores messages in a Redis list.
 */
class RedisSpool extends \Swift_ConfigurableSpool
{
    /**
     * @var RedisClient
     */
    protected $redis;

    /**
     * @var string
     */
    protected $key;

    public function setRedis($redis)
    {
        $this->redis = $redis;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isStarted()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function queueMessage(\Swift_Mime_Message $message)
    {
        // To enqueue a message, just push it to the right side of the list.
        $this->redis->rpush($this->key, serialize($message));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function flushQueue(\Swift_Transport $transport, &$failedRecipients = null)
    {
        if (!$this->redis->llen($this->key)) {
            return 0;
        }
        if (!$transport->isStarted()) {
            $transport->start();
        }

        $failedRecipients = (array) $failedRecipients;
        $count = 0; $time = time();

        while (($message = unserialize($this->redis->lpop($this->key)))) {
            $count += $transport->send($message, $failedRecipients);

            if ($this->getMessageLimit() && $count >= $this->getMessageLimit()) {
                break;
            }

            if ($this->getTimeLimit() && (time() - $time) >= $this->getTimeLimit()) {
                break;
            }
        }

        return $count;
    }
}
