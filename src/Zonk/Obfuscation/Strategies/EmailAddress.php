<?php

namespace Zonk\Obfuscation\Strategies;

class EmailAddress extends FakerAwareStrategy
{
    /**
     * @inheritdoc
     */
    public function obfuscate($value = null)
    {
        return $this->generator->safeEmail;
    }
}
