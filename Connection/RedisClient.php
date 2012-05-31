<?php

namespace Lt\Bundle\RedisBundle\Connection;

use Predis\Client as BaseClient;

class RedisClient extends BaseClient
{
    function __construct($parameters = null, $options = null, ConnectionFactory $factory = null)
    {
        if (empty($parameters['password'])) {
            unset($parameters['password']);
        }

        if (!empty($factory)) {
            $options['connections'] = $factory;
        }

        parent::__construct($parameters, $options);
    }
}