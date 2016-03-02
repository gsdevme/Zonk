<?php

namespace Zonk;

class Configuration implements ConfigurationInterface
{
    private $config;

    public function __construct($config)
    {
        $this->config = array_merge(
            [
                'operations' => [],
                'database'   => [],
            ],
            $config
        );
    }

    /**
     * @inheritdoc
     */
    public function getConfigKey($key)
    {
        if (!$this->hasConfigKey($key)) {
            return null;
        }

        return $this->config[$key];
    }

    /**
     * @inheritdoc
     */
    public function hasConfigKey($key)
    {
        return isset($this->config[$key]);
    }
}
