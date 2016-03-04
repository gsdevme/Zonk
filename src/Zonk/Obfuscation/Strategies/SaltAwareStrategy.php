<?php

namespace Zonk\Obfuscation\Strategies;

abstract class SaltAwareStrategy implements StrategyInterface
{
    /** @var string */
    protected $salt;

    /**
     * @param $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }
}
