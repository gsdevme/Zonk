<?php

namespace Zonk\Obfuscation\Strategies;

interface StrategyInterface
{
    /**
     * @param $value
     *
     * @return mixed
     */
    public function obfuscate($value = null);
}
