<?php

namespace Zonk\Database\Common;

use Zonk\Database\ConnectionProvider;

trait DisabledForeignKeyConstraintsTrait
{
    /**
     * @param ConnectionProvider $connectionProvider
     * @param callable           $callable
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function doDisabledForeignKeyConstraints(ConnectionProvider $connectionProvider, callable $callable)
    {
        $connection = $connectionProvider->getConnection();

        $connection->executeQuery('SET foreign_key_checks=0');
        $callable();
        $connection->executeQuery('SET foreign_key_checks=1');
    }
}
