<?php

namespace Zonk\Database;

use Zonk\Configuration;
use Doctrine\DBAL\DriverManager;

class ConnectionBuilder
{
    /**
     * @param Configuration $configuration
     *
     * @return \Doctrine\DBAL\Connection
     * @throws \Doctrine\DBAL\DBALException
     */
    public function build(Configuration $configuration)
    {
        if (!$configuration->hasConfigKey('database')) {
            throw new \RuntimeException(sprintf('No database configuration'));
        }

        $config = new \Doctrine\DBAL\Configuration();
        $config->setAutoCommit(false);

        $params = array_merge([
            'dbname'   => 'database',
            'user'     => 'root',
            'password' => 'root',
            'host'     => '127.0.0.1',
            'driver'   => 'pdo_mysql',
        ], $configuration->getConfigKey('database'));

        $conn = DriverManager::getConnection($params, $config);

        return $conn;
    }
}