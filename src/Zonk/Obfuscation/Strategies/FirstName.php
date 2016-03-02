<?php

namespace Zonk\Obfuscation\Strategies;

class FirstName extends FakerAwareStrategy
{
    /**
     * @inheritdoc
     */
    public function obfuscate($value = null)
    {
        return md5($value);
    }
}
