<?php

namespace Zonk;

class SingleTableConfiguration implements ConfigurationInterface
{
    private $config;

    public function __construct($config, $table)
    {
        $this->config = array_merge(
            [
                'operations' => [],
                'database'   => [],
            ],
            $config
        );

        foreach ($this->config['operations'] as &$operation) {
            if (isset($operation['tables'])) {
                foreach ($operation['tables'] as $key => $value) {
                    if (is_string($value) && $table != $value) {
                        unset($operation['tables'][$key]);
                    }

                    if (is_string($key) && is_array($value) && $key != $table) {
                        unset($operation['tables'][$key]);
                    }
                }
            }
        }
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
