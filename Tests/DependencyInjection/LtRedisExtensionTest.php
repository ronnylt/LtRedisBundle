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

    private function basicYmlConfig()
    {
        return <<<YML
connections:
    default:
        alias: default
YML;
    }
}
