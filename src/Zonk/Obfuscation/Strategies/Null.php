<?php

namespace Zonk\Obfuscation\Strategies;

class Null implements StrategyInterface
{
    /**
     * @inheritdoc
     */
    public function obfuscate($value = null)
    {
        return null;
    }
}
