<?php

namespace Zonk\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Input\InputInterface;
use Zonk\Console\Command\ZonkCommand;

class Application extends SymfonyApplication
{
    /** @var string */
    private $binary;

    /**
     * Application constructor.
     *
     * @param string $version
     */
    public function __construct($version, $binary)
    {
        parent::__construct('Zonk', $version);

        $this->binary = $binary;
    }

    /**
     * @inheritdoc
     */
    protected function getCommandName(InputInterface $input)
    {
        return ZonkCommand::NAME;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultCommands()
    {
        $defaultCommands[] = new HelpCommand();
        $defaultCommands[] = new ZonkCommand();

        return $defaultCommands;
    }

    /**
     * @inheritdoc
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();

        return $inputDefinition;
    }

    /**
     * @return string
     */
    public function getBinary()
    {
        return $this->binary;
    }
}
