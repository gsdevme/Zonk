<?php

namespace Zonk\Obfuscation\Strategies;

class BasicString extends SaltAwareStrategy
{
    /**
     * @inheritdoc
     */
    public function obfuscate($value = null)
    {
        return md5(sprintf('%s-%s', $this->salt, $value));
    }
}
