<?php

namespace Zonk\Database\Common;

use Doctrine\DBAL\Schema\Index;
use Zonk\Database\ConnectionProvider;

trait PrimaryKeyTrait
{
    /**
     * @param ConnectionProvider $connectionProvider
     * @param                    $tableName
     *
     * @return null|string
     */
    public function getPrimaryKey(ConnectionProvider $connectionProvider, $tableName)
    {
        $indexes = $connectionProvider
            ->getConnection()
            ->getSchemaManager()
            ->listTableIndexes($tableName);

        if (!isset($indexes['primary'])) {
            return null;
        }

        /** @var Index $primary */
        $primary = $indexes['primary'];

        return implode(', ', $primary->getColumns());
    }
}
