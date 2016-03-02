<?php

namespace Zonk\Database;

use Doctrine\DBAL\Connection;

class ConnectionProvider
{
    private $connection;
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        if (!$this->connection->isConnected()) {
            $this->connection->connect();
        }

        return $this->connection;
    }

    /**
     * @return \Doctrine\DBAL\Driver\Connection
     */
    public function getWrapperConnection()
    {
        return $this->connection->getWrappedConnection();
    }
}
