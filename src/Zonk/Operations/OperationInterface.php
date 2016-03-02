<?php

namespace Zonk\Operations;

use Zonk\Configuration;

interface OperationInterface
{
    /**
     * @param Configuration $configuration
     *
     * @return bool
     */
    public function doOperation(Configuration $configuration);

    /**
     * @return string
     */
    public function getName();
}
