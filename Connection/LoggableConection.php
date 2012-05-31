<?php

namespace Lt\Bundle\RedisBundle\Connection;

use Predis\Network\IConnectionSingle;
use Predis\Commands\ICommand;

use Lt\Bundle\RedisBundle\Logger\RedisLogger;

class LoggableConection implements IConnectionSingle
{
    /**
     * @var IConnectionSingle
     */
    protected $connection;

    /**
     * @var RedisLogger
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param IConnectionSingle $connection
     */
    public function __construct(IConnectionSingle $connection, RedisLogger $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    /**
     * Opens the connection.
     */
    public function connect()
    {
        $this->connection->connect();
    }

    /**
     * Closes the connection.
     */
    public function disconnect()
    {
        $this->connection->disconnect();
    }

    /**
     * Returns if the connection is open.
     *
     * @return Boolean
     */
    public function isConnected()
    {
        return $this->isConnected();
    }

    /**
     * Write a Redis command on the connection.
     *
     * @param ICommand $command Instance of a Redis command.
     */
    public function writeCommand(ICommand $command)
    {
        return $this->connection->writeCommand($command);
    }

    /**
     * Reads the reply for a Redis command from the connection.
     *
     * @param ICommand $command Instance of a Redis command.
     * @return mixed
     */
    public function readResponse(ICommand $command)
    {
        return $this->connection->readResponse($command);
    }

    /**
     * Writes a Redis command to the connection and reads back the reply.
     *
     * @param ICommand $command Instance of a Redis command.
     * @return mixed
     */
    public function executeCommand(ICommand $command)
    {
        $startTime = microtime(true);

        $result = $this->connection->executeCommand($command);

        $executionTime  = microtime(true) - $startTime;
        $error = $result instanceof ResponseError ? (string) $result : false;
        $this->logger->logCommand((string)$command, $executionTime, $this->getParameters()->alias, $error);

        return $result;
    }

    /**
     * Returns a string representation of the connection.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->connection->__toString();
    }

    /**
     * Returns the underlying resource used to communicate with a Redis server.
     *
     * @return mixed
     */
    public function getResource()
    {
        return $this->connection->getResource();
    }

    /**
     * Gets the parameters used to initialize the connection object.
     *
     * @return IConnectionParameters
     */
    public function getParameters()
    {
        return $this->connection->getParameters();
    }

    /**
     * Pushes the instance of a Redis command to the queue of commands executed
     * when the actual connection to a server is estabilished.
     *
     * @param ICommand $command Instance of a Redis command.
     * @return IConnectionParameters
     */
    public function pushInitCommand(ICommand $command)
    {
        return $this->connection->pushInitCommand($command);
    }

    /**
     * Reads a reply from the server.
     *
     * @return mixed
     */
    public function read()
    {
        return $this->connection->read();
    }
}