<?php

namespace Zonk\Obfuscation;

use Faker\Factory;
use Zonk\Obfuscation\Strategies\FakerAwareStrategy;
use Zonk\Obfuscation\Strategies\StrategyInterface;

class StrategyRegistry
{
    private $registry;

    private $generator;

    /**
     * StrategyProvider constructor.
     */
    public function __construct()
    {
        $this->registry = [];
        $this->generator = Factory::create();
    }

    /**
     * @param $key
     * @param $strategy
     */
    public function add($key, $strategy)
    {
        $instance = new $strategy;

        if (!$instance instanceof StrategyInterface) {
            throw new \RuntimeException(sprintf('flksrnfsf'));
        }

        if ($instance instanceof FakerAwareStrategy) {
            $instance->setFakerGenerator($this->generator);
        }

        $this->registry[$key] = $instance;
    }

    /**
     * @param $key
     *
     * @return StrategyInterface
     */
    public function find($key)
    {
        if (!isset($this->registry[$key])) {
            return null;
        }

        return $this->registry[$key];
    }
}
