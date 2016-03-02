<?php

namespace Zonk\Database\Common;

use Zonk\Database\CapsuleProvider;

trait DisabledForeignKeyConstraintsTrait
{
    /**
     * @param CapsuleProvider $capsuleProvider
     * @param callable        $callable
     */
    public function doDisabledForeignKeyConstraints(CapsuleProvider $capsuleProvider, callable $callable)
    {
        $capsule = $capsuleProvider->getCapsule();
        $connection = $capsule->getConnection();

        $connection->statement('SET foreign_key_checks=0');
        $callable();
        $connection->statement('SET foreign_key_checks=1');
    }
}
