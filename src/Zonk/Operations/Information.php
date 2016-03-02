<?php

namespace Zonk\Operations;

use Psr\Log\LoggerInterface;
use Zonk\ConfigurationInterface;
use Zonk\Database\Common\ListTableNamesTrait;
use Zonk\Database\ConnectionProvider;

class Information implements OperationInterface
{
    use ListTableNamesTrait;

    /** @var ConnectionProvider */
    protected $connectionProvider;

    /** @var LoggerInterface */
    private $logger;

    /**
     * Information constructor.
     *
     * @param ConnectionProvider $connectionProvider
     * @param LoggerInterface    $logger
     */
    public function __construct(ConnectionProvider $connectionProvider, LoggerInterface $logger)
    {
        $this->connectionProvider = $connectionProvider;
        $this->logger = $logger;
    }

    public function getName()
    {
        return 'Information';
    }

    /**
     * @param ConfigurationInterface $configuration
     */
    public function doOperation(ConfigurationInterface $configuration)
    {
        $database = $configuration->getConfigKey('database');

        $tables = $this->getListTableNames($this->connectionProvider);

        $this->logger->warning(sprintf('Zonk Performing Opertations on %s', $database['dbname']));
        $this->logger->info(sprintf('Number of tables: %d', count($tables)));
    }
}
