<?php

namespace Lt\Bundle\RedisBundle\Connection;

use Predis\Profiles\IServerProfile;
use Lt\Bundle\RedisBundle\Logger\RedisLogger;

class ConnectionFactory extends \Predis\ConnectionFactory
{
    /**
     * @var RedisLogger
     */
    protected $logger;

    public function __construct(RedisLogger $logger)
    {
        parent::__construct();
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function create($parameters, IServerProfile $profile = null)
    {
        $connection = parent::create($parameters, $profile);
        $connection = new LoggableConection($connection, $this->logger);

        return $connection;
    }
}