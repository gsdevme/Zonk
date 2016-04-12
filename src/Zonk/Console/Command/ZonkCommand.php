<?php

namespace Zonk\Console\Command;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Zonk\Console\Application;
use Zonk\Database\Common\ListTableNamesTrait;
use Zonk\Database\ConnectionBuilder;
use Zonk\Database\ConnectionProvider;
use Zonk\Monolog\OutputHandler;
use Zonk\Operations\Delete;
use Zonk\Operations\Obfuscate;
use Zonk\Operations\OperationInterface;
use Zonk\Operations\Truncate;
use Zonk\YmlConfigurationFactory;

class ZonkCommand extends Command
{
    use ListTableNamesTrait;

    const NAME                = 'zonk';
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
        $this->addOption(
            'table',
            't',
            InputOption::VALUE_REQUIRED,
            'Run only this table',
            false
        );
        $this->addOption(
            'process-per-table',
            'f',
            InputOption::VALUE_NONE,
            'Fork each table into its own process'
        );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ymlConfigurationFactory = new YmlConfigurationFactory();
        $configuration = $ymlConfigurationFactory->createFromYml(
            $input->getOption('config-file'),
            $input->getOption('table')
        );

        $connection = (new ConnectionBuilder())->build($configuration);
        $connectionProvider = new ConnectionProvider($connection);

        if ($input->getOption('process-per-table')) {
            $this->fork($connectionProvider, $input->getOption('config-file'));

            return;
        }

        //$this->doBanner($output);
        $logger = $this->getLogger($output);

        $operations = [
            new Truncate($connectionProvider, $logger),
            new Delete($connectionProvider, $logger),
            new Obfuscate($connectionProvider, $logger),
        ];

        /** @var OperationInterface $operation */
        foreach ($operations as $operation) {
            //$this->logger->warning('Operation: '.$operation->getName().' Started');
            $operation->doOperation($configuration);
            //$this->logger->warning('Operation: '.$operation->getName().' Finished');
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

        $logger = new Logger('default', [$outputHandler]);
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

        $output->write('<info>'.$banner.'</info>');

    }

    /**
     * @param ConnectionProvider $connectionProvider
     * @param                    $configFile
     */
    private function fork(ConnectionProvider $connectionProvider, $configFile)
    {
        $config = $configFile;
        $queue = [];
        $running = [];
        $tables = $this->getListTableNames($connectionProvider);

        /** @var Application $application */
        $application = $this->getApplication();

        foreach ($tables as $table) {
            $command = sprintf('%s -t %s -c %s', $application->getBinary(), $table, $config);
            $process = new Process($command);
            $queue[] = $process;
        }

        /** @var Process $process */
        do {

            echo count($queue);
            foreach ($queue as &$process) {
                if (count($running) < 5) {
                    $running[] = $process;
                    $process->start();
                    unset($process);
                }
            }

            echo count($queue);
            die;

            foreach ($running as &$process) {
                echo $process->getOutput();

                if (!$process->isRunning()) {
                    unset($process);
                }
            }


            usleep(50000);
        } while (count($running) > 0);

    }
}
