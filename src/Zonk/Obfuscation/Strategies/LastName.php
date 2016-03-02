<?php

namespace Zonk\Obfuscation\Strategies;

class LastName extends FakerAwareStrategy
{
    /**
     * @inheritdoc
     */
    public function obfuscate($value = null)
    {
        return md5($value);
    }
}
