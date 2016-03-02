<?php

namespace Zonk;

class Configuration
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
     * @param $key
     *
     * @return null|mixed
     */
    public function getConfigKey($key)
    {
        if (!$this->hasConfigKey($key)) {
            return null;
        }

        return $this->config[$key];
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function hasConfigKey($key)
    {
        return isset($this->config[$key]);
    }
}
