<?php

namespace Lt\Bundle\RedisBundle\Tests\Session\Storage\Handler;

use Lt\Bundle\RedisBundle\Session\Storage\Handler\RedisSessionHandler;

class RedisSessionHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $redis;

    private $keyPrefix = 'redis:lt:bundle:session';

    protected function setUp()
    {
        $this->redis = $this
            ->getMockBuilder('Lt\Bundle\RedisBundle\Connection\RedisClient')
            ->setMethods(array('get', 'set', 'expire', 'del'))
            ->getMock();
    }

    protected function tearDown()
    {
        unset($this->redis);
    }

    public function testRead()
    {
        $sessionId = rand();

        $this->redis
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo($this->keyPrefix . ':' . $sessionId))
        ;

        $handler = new RedisSessionHandler($this->redis, array(), $this->keyPrefix);
        $handler->read($sessionId);
    }

    public function testDestroy()
    {
        $sessionId = rand();

        $this->redis
            ->expects($this->once())
            ->method('del')
            ->with($this->equalTo($this->keyPrefix . ':' . $sessionId))
        ;

        $handler = new RedisSessionHandler($this->redis, array(), $this->keyPrefix);
        $handler->destroy($sessionId);
    }

    public function testWriteWithNoExpiration()
    {
        $sessionId = rand();
        $data = md5(rand());

        $this->redis
            ->expects($this->once())
            ->method('set')
            ->with($this->equalTo($this->keyPrefix . ':' . $sessionId), $this->equalTo($data))
        ;

        $handler = new RedisSessionHandler($this->redis, array(), $this->keyPrefix);
        $handler->write($sessionId, $data);
    }

    public function testWriteWithExpiration()
    {
        $sessionId = rand();
        $data = md5(rand());
        $expiration = rand();

        $this->redis
            ->expects($this->once())
            ->method('set')
            ->with($this->equalTo($this->keyPrefix . ':' . $sessionId), $this->equalTo($data))
        ;

        $this->redis
            ->expects($this->once())
            ->method('expire')
            ->with($this->equalTo($this->keyPrefix . ':' . $sessionId), $this->equalTo($expiration))
        ;

        $handler = new RedisSessionHandler($this->redis, array('cookie_lifetime' => $expiration), $this->keyPrefix);
        $handler->write($sessionId, $data);
    }

    public function testGetKeyWithPrefix()
    {
        $sessionId = rand();
        $handler = new RedisSessionHandler($this->redis, array(), $this->keyPrefix);
        $method = new \ReflectionMethod($handler, 'getKey');
        $method->setAccessible(true);

        $this->assertEquals($this->keyPrefix . ':' . $sessionId, $method->invoke($handler, $sessionId));
    }

    public function testGetKeyWithoutPrefix()
    {
        $sessionId = rand();
        $handler = new RedisSessionHandler($this->redis, array(), '');
        $method = new \ReflectionMethod($handler, 'getKey');
        $method->setAccessible(true);

        $this->assertEquals($sessionId, $method->invoke($handler, $sessionId));
    }

}