<?php

namespace Lt\Bundle\RedisBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Lt\Bundle\RedisBundle\Logger\RedisLogger;

/**
 * RedisDataCollector
 */
class RedisDataCollector extends DataCollector
{
    /**
     * @var RedisLogger
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param RedisLogger $logger
     */
    public function __construct(RedisLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        if (null === $this->logger) {
            $commands = array();
        } else {
            $commands =  $this->logger->getCommands();
        }

        $this->data = array(
            'commands' => $commands,
        );
    }

    /**
     * Returns an array of collected commands.
     *
     * @return array
     */
    public function getCommands()
    {
        return $this->data['commands'];
    }

    /**
     * Returns the number of collected commands.
     *
     * @return integer
     */
    public function getCommandsCount()
    {
        return count($this->data['commands']);
    }

    public function getRepetitions()
    {
        $repetitions = array();

        foreach ($this->getCommands() as $command) {

            if (empty($repetitions[$command['command']])) {
                $repetitions[$command['command']] = 1;
            } else {
                $repetitions[$command['command']] ++;
            }
        }

        arsort($repetitions);

        return $repetitions;
    }

    /**
     * Returns the execution time of all collected commands in seconds.
     *
     * @return float
     */
    public function getExecutionTime()
    {
        $time = 0;
        foreach ($this->data['commands'] as $command) {
            $time += $command['executionTime'];
        }

        return $time;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lt_redis';
    }
}