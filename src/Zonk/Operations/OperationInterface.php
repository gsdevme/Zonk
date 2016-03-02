<?php

namespace Zonk\Operations;

use Zonk\ConfigurationInterface;

interface OperationInterface
{
    /**
     * @param ConfigurationInterface $configuration
     */
    public function doOperation(ConfigurationInterface $configuration);

    /**
     * @return string
     */
    public function getName();
}
