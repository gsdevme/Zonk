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
            'list-tables',
            'l',
            InputOption::VALUE_NONE,
            'List each table'
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

        if ($input->getOption('list-tables')) {
            $commands = $this->listTableCommands($connectionProvider, $input->getOption('config-file'));

            return $output->write(implode(PHP_EOL, $commands));
        }

        $logger = $this->getLogger($output);

        $operations = [
            new Truncate($connectionProvider, $logger),
            new Obfuscate($connectionProvider, $logger),
        ];

        /** @var OperationInterface $operation */
        foreach ($operations as $operation) {
            if (!$input->getOption('table')) {
                $this->logger->warning('Operation: '.$operation->getName().' Started');
            }

            $operation->doOperation($configuration);

            if (!$input->getOption('table')) {
                $this->logger->warning('Operation: '.$operation->getName().' Finished');
            }
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
     * @param ConnectionProvider $connectionProvider
     * @param                    $configFile
     */
    private function listTableCommands(ConnectionProvider $connectionProvider, $configFile)
    {
        $config = $configFile;
        $tables = $this->getListTableNames($connectionProvider);
        $commands = [];

        /** @var Application $application */
        $application = $this->getApplication();

        foreach ($tables as $table) {
            $commands[] = sprintf('%s -t %s -c %s', $application->getBinary(), $table, $config);
        }

        return $commands;
    }
}
