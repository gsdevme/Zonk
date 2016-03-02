<?php

namespace Zonk\Database\Common;

use Zonk\Database\ConnectionProvider;

trait CountTableRowsTrait
{
    /**
     * @param ConnectionProvider $connectionProvider
     *
     * @return array
     */
    public function getTableRows(ConnectionProvider $connectionProvider, $tableName)
    {
        $count = $connectionProvider
            ->getConnection()
            ->executeQuery(sprintf('SELECT COUNT(1) FROM %s LIMIT 1', $tableName))
            ->fetchColumn(0);

        return (int)$count;
    }
}
