<?php

namespace Zonk\Obfuscation\Strategies;

use Faker\Generator;

abstract class FakerAwareStrategy implements StrategyInterface
{
    /** @var Generator */
    protected $generator;

    /**
     * @param Generator $generator
     */
    public function setFakerGenerator(Generator $generator)
    {
        $this->generator = $generator;
    }
}
