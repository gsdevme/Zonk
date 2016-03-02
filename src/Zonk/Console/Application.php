<?php

namespace Zonk\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Input\InputInterface;
use Zonk\Console\Command\ZonkCommand;

class Application extends SymfonyApplication
{
    /**
     * Application constructor.
     *
     * @param string $version
     */
    public function __construct($version)
    {
        parent::__construct('Zonk', $version);
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
}
