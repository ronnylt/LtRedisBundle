<?php


namespace Lt\Bundle\RedisBundle\Session\Storage\Handler;

use Lt\Bundle\RedisBundle\Connection\RedisClient;

/**
 * Redis based session storage.
 */
class RedisSessionHandler implements \SessionHandlerInterface
{
    /**
     * @var RedisClient
     */
    protected $redis;

    /**
     * Array of option
     * @var array
     */
    protected $options;

    /**
     * Prefix to use in the keys used to store session data.
     * @var string
     */
    protected $keyPrefix;

    /**
     * Redis session storage constructor
     *
     * @param RedisClient $redis   Redis database connection
     * @param array $options       Session options
     * @param string $prefix       Key prefix to use for writing session data in Redis.
     */
    public function __construct(RedisClient $redis, $options = array(), $keyPrefix = 'session')
    {
        $this->redis = $redis;
        $this->options = $options;
        $this->keyPrefix = $keyPrefix;
    }

    /**
     * {@inheritDoc}
     */
    public function open($savePath, $sessionName)
    {
        // Not required, connections to redis open on-the-fly.
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function close()
    {
        // Not required, connections to redis are closed automatically.
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function read($sessionId)
    {
        return $this->redis->get($this->getKey($sessionId)) ?: '';
    }

    /**
     * {@inheritDoc}
     */
    public function write($sessionId, $data)
    {
        $this->redis->set($this->getKey($sessionId), $data);

        if (isset($this->options['cookie_lifetime']) && 0 < ($expires = (int) $this->options['cookie_lifetime'])) {
            $this->redis->expire($this->getKey($sessionId), $expires);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function destroy($sessionId)
    {
        $this->redis->del($this->getKey($sessionId));

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function gc($lifetime)
    {
        // Not required here because keys will auto expire based on 'cookie_lifetime'.
        return true;
    }

    /**
     * Generate the key name based on the session ID and a predefined prefix (namespace).
     *
     * @param string $id The session ID.
     * @return string The name of the redis key.
     */
    protected function getKey($id)
    {
        return empty($this->keyPrefix) ? $id : $this->keyPrefix . ':' . $id;
    }
}
