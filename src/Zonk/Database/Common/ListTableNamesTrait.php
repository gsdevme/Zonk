<?php

namespace Zonk\Database\Common;

use Zonk\Database\ConnectionProvider;

trait ListTableNamesTrait
{
    /**
     * @param ConnectionProvider $connectionProvider
     *
     * @return array
     */
    public function getListTableNames(ConnectionProvider $connectionProvider)
    {
        $connection = $connectionProvider->getConnection();
        $tables = $connection->getSchemaManager()->listTableNames();

        return $tables;
    }
}
