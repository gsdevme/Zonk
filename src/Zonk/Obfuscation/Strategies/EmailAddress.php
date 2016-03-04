<?php

namespace Zonk\Obfuscation\Strategies;

class EmailAddress extends SaltAwareStrategy
{
    /**
     * @inheritdoc
     */
    public function obfuscate($value = null)
    {
        return sprintf('%s@example.com', md5(sprintf('%s-%s', $this->salt, $value)));
    }
}
