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

        if (isset($config['session'])) {
            $this->loadSession($config, $container, $loader);
        }

        if (isset($config['monolog'])) {
            $this->loadMonolog($config, $container);
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

    /**
     * Loads the session configuration.
     *
     * @param array $config A configuration array
     * @param ContainerBuilder $container A ContainerBuilder instance
     * @param XmlFileLoader $loader
     */
    protected function loadSession(array $config, ContainerBuilder $container, XmlFileLoader $loader)
    {
        $loader->load('session.xml');

        $container->setParameter('lt_redis.session.handler.connection', $config['session']['connection']);
        $container->setAlias('lt_redis.session.handler.connection', sprintf('lt_redis.%s_connection', $container->getParameter('lt_redis.session.handler.connection')));

        $container->setParameter('lt_redis.session.handler.prefix', $config['session']['prefix']);
    }

    /**
     * Loads the monolog handler configuration.
     *
     * @param array $config A configuration array
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    protected function loadMonolog(array $config, ContainerBuilder $container)
    {
        $def = new Definition('Lt\Bundle\RedisBundle\Monolog\Handler\RedisHandler');
        $def->setPublic(false);
        $def->addMethodCall('setRedis', array(new Reference(sprintf('lt_redis.%s', $config['monolog']['connection']))));
        $def->addMethodCall('setKey', array($config['monolog']['key']));
        $container->setDefinition('monolog.handler.lt_redis', $def);
    }
}
