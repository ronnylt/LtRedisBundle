<?php

namespace Lt\Bundle\RedisBundle\Connection;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ConnectionRegistry
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getConnection($name = 'default  ')
    {
        return $this->container->get('lt_redis.' .$name );
    }
}