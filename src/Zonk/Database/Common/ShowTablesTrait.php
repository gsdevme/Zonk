<?php

namespace Zonk\Database\Common;

use Zonk\Database\CapsuleProvider;

trait ShowTablesTrait
{
    /**
     * @param CapsuleProvider $capsuleProvider
     *
     * @return array
     */
    public function getShowTables(CapsuleProvider $capsuleProvider)
    {
        $capsule = $capsuleProvider->getCapsule();
        $connection = $capsule->getConnection();

        $tables = (array)$connection->select('SHOW TABLES');

        $tables = array_map(function ($v) {
            return array_values((array)$v)[0];
        }, $tables);

        return $tables;
    }
}
