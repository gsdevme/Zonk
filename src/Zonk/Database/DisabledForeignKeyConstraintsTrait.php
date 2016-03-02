<?php

namespace Zonk\Database;

trait DisabledForeignKeyConstraintsTrait
{
    /** @var CapsuleProvider */
    private $provider;

    /**
     * @param CapsuleProvider $capsuleProvider
     */
    public function setCapsuleProvider(CapsuleProvider $capsuleProvider)
    {
        $this->provider = $capsuleProvider;
    }

    /**
     * @param callable $callable
     */
    public function doDisabledForeignKeyConstraints(callable $callable)
    {
        $capsule = $this->provider->getCapsule();
        $connection = $capsule->getConnection();

        $connection->statement('SET foreign_key_checks=0');
        $callable();
        $connection->statement('SET foreign_key_checks=1');
    }
}
