<?php

namespace Zonk\Obfuscation\Strategies;

class EmailAddress implements StrategyInterface
{
    /**
     * @inheritdoc
     */
    public function obfuscate($value = null)
    {
        return sprintf('%s@example.com', hash('sha256', $value));
    }
}
