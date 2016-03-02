<?php

namespace Zonk;

interface ConfigurationInterface
{
    /**
     * @param $key
     *
     * @return null|mixed
     */
    public function getConfigKey($key);

    /**
     * @param $key
     *
     * @return mixed
     */
    public function hasConfigKey($key);
}
