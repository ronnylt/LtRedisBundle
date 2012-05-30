<?php

namespace Lt\Bundle\RedisBundle\Logger;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

class RedisLogger
{
    protected $logger;
    protected $commands = array();

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public function logCommand($command, $executionTime, $connection, $error = false)
    {
        $this->commands[] = array(
            'command' => $command,
            'executionTime' => $executionTime,
            'connection' => $connection,
            'error' => $error,
        );

        if (null !== $this->logger) {
            if ($error) {
                $this->logger->err('Error in Redis Command "' . $command . '" failed with error: (' . $error . ')');
            } else {
                $this->logger->info('Executing Redis Command "' . $command . '"');
            }
        }
    }

    public function getNumberOfCommands()
    {
        return count($this->commands);
    }

    public function getCommands()
    {
        return $this->commands;
    }

    public function getCommandsCount()
    {
        return $this->commandsCount;
    }
}