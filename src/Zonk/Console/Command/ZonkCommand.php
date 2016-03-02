<?php

namespace Zonk\Console\Command;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zonk\Database\CapsuleBuilder;
use Zonk\Database\CapsuleProvider;
use Zonk\Monolog\OutputHandler;
use Zonk\Operations\Information;
use Zonk\Operations\OperationInterface;
use Zonk\Operations\Truncate;
use Zonk\YmlConfigurationFactory;

class ZonkCommand extends Command
{
    const NAME = 'zonk';
    const DEFAULT_CONFIG_FILE = '/etc/zonk/zonk.yml';

    /** @var LoggerInterface */
    private $logger;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName(self::NAME);

        $this->addOption(
            'config-file',
            'c',
            InputOption::VALUE_REQUIRED,
            'Configuration file path',
            self::DEFAULT_CONFIG_FILE
        );
        $this->addOption('output-file', 'o', InputOption::VALUE_REQUIRED, 'Output file', realpath(getcwd()).'zonked');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doBanner($output);

        $ymlConfigurationFactory = new YmlConfigurationFactory();
        $configuration = $ymlConfigurationFactory->createFromYml($input->getOption('config-file'));

        $capsule = (new CapsuleBuilder())->build($configuration);
        $capsuleProvider = new CapsuleProvider($capsule);

        $logger = $this->getLogger($output);

        $operations = [
            new Information($capsuleProvider, $logger),
            new Truncate($capsuleProvider, $logger),
        ];

        /** @var OperationInterface $operation */
        foreach ($operations as $operation) {
            $this->logger->warning('Operation: '.$operation->getName().' Started');
            $operation->doOperation($configuration);
        }

        return 0;
    }

    /**
     * @param OutputInterface|null $output
     *
     * @return Logger|LoggerInterface
     */
    private function getLogger(OutputInterface $output = null)
    {
        if ($output === null && $this->logger instanceof LoggerInterface) {
            return $this->logger;
        }

        $outputHandler = new OutputHandler();
        $outputHandler->setOutput($output);

        $logger = new Logger('default');
        $logger->setHandlers([$outputHandler]);

        $this->logger = $logger;

        return $this->logger;
    }

    /**
     * @param OutputInterface $output
     */
    private function doBanner(OutputInterface $output)
    {
        $version = $this->getApplication()->getVersion();

        $banner = <<<BANNER
========================
Zonk! - Version: $version
=========================

BANNER;

        $output->write('<info>' . $banner. '</info>');

    }
}
