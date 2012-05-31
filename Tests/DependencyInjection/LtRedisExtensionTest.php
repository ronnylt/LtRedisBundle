<?php

namespace Lt\Bundle\RedisBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;
use Lt\Bundle\RedisBundle\DependencyInjection\LtRedisExtension;

class LtRedisExtensionTest extends \PHPUnit_Framework_TestCase
{

    public function testBasicConfigurationLoad()
    {
        $extension = new LtRedisExtension();
        $parser = new Parser();

        $config = $parser->parse($this->basicYmlConfig());
        $extension->load(array($config), $container = new ContainerBuilder());

        $services = $container->getServiceIds();

        $this->assertContains('lt_redis.connection_factory', $services);
        $this->assertContains('lt_redis.logger', $services);
        $this->assertContains('lt_redis.default', $services);
        $this->assertContains('lt_redis.default_connection', $services);
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testRequiredAtLeastOneConnection()
    {
        $extension = new LtRedisExtension();
        $config = array();
        $extension->load(array($config), $container = new ContainerBuilder());
    }

    public function testSessionConfigurationLoad()
    {
        $extension = new LtRedisExtension();
        $parser = new Parser();

        $config = $parser->parse($this->sessionYmlConfig());
        $extension->load(array($config), $container = new ContainerBuilder());

        $this->assertTrue($container->hasDefinition('lt_redis.session.handler'));
        $this->assertTrue($container->hasParameter('lt_redis.session.handler.connection'));
        $this->assertEquals($container->getParameter('lt_redis.session.handler.connection'), 'session');

        $this->assertTrue($container->hasAlias('lt_redis.session.handler.connection'));
        $this->assertEquals($container->getAlias('lt_redis.session.handler.connection'), 'lt_redis.session_connection');

        $this->assertTrue($container->hasParameter('lt_redis.session.handler.prefix'));
        $this->assertEquals($container->getParameter('lt_redis.session.handler.prefix'), 'lt_redis_session');
    }


    private function basicYmlConfig()
    {
        return <<<YML
connections:
    default:
        alias: default
YML;
    }

    private function sessionYmlConfig()
    {
        return <<<YML
connections:
    session:
        alias: session

session:
    connection: session
    prefix: lt_redis_session
YML;
    }
}
