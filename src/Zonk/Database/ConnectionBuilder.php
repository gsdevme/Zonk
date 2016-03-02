<?php

namespace Zonk\Database;

use Doctrine\DBAL\DriverManager;
use Zonk\ConfigurationInterface;

class ConnectionBuilder
{
    /**
     * @param ConfigurationInterface $configuration
     *
     * @return \Doctrine\DBAL\Connection
     * @throws \Doctrine\DBAL\DBALException
     */
    public function build(ConfigurationInterface $configuration)
    {
        if (!$configuration->hasConfigKey('database')) {
            throw new \RuntimeException(sprintf('No database configuration'));
        }

        $config = new \Doctrine\DBAL\Configuration();
        $config->setAutoCommit(false);

        $params = array_merge(
            [
                'dbname'   => 'database',
                'user'     => 'root',
                'password' => 'root',
                'host'     => '127.0.0.1',
                'driver'   => 'pdo_mysql',
            ],
            $configuration->getConfigKey('database')
        );

        $conn = DriverManager::getConnection($params, $config);

        return $conn;
    }
}
