<?php

namespace Lt\Bundle\RedisBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class LtRedisExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $config = $this->processConfiguration(new Configuration\Configuration(), $configs);

        foreach ($config['connections'] as $connection) {
            $this->loadConnection($connection, $container);
        }
    }

    /**
     * Loads connections.
     *
     * @param array $config A connection config options.
     * @param ContainerBuilder $container The ContainerBuilder instance
     */
    protected function loadConnection(array $config, ContainerBuilder $container)
    {
        $connectionDef = new Definition('Lt\Bundle\RedisBundle\Connection\RedisClient'); //TODO: Use configurable class.
        $connectionDef->setScope(ContainerInterface::SCOPE_CONTAINER);

        $parameters = array(
            'host' => $config['host'],
            'port' => $config['port'],
            'database' => $config['database'],
            'password' => $config['auth']
        );

        $connectionDef->addArgument($parameters);
        $connectionDef->addArgument($config['options']);

        if ($config['logging']) {
            $connectionDef->addArgument(new Reference('lt_redis.connection_factory'));
        }

        $container->setDefinition(sprintf('lt_redis.%s', $config['alias']), $connectionDef);
        $container->setAlias(sprintf('lt_redis.%s_connection', $config['alias']), sprintf('lt_redis.%s', $config['alias']));
    }

}
