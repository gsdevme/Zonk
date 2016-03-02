<?php

namespace Zonk\Operations;

use Psr\Log\LoggerInterface;
use Zonk\Configuration;
use Zonk\Database\CapsuleProvider;

class Information implements OperationInterface
{
    /** @var CapsuleProvider */
    protected $capsuleProvider;

    /** @var LoggerInterface */
    private $logger;

    /**
     * Truncate constructor.
     *
     * @param CapsuleProvider $capsuleProvider
     * @param LoggerInterface $logger
     */
    public function __construct(CapsuleProvider $capsuleProvider, LoggerInterface $logger)
    {
        $this->capsuleProvider = $capsuleProvider;
        $this->logger = $logger;
    }

    public function getName()
    {
        return 'Information';
    }

    /**
     * @param Configuration $configuration
     *
     * @return bool
     */
    public function doOperation(Configuration $configuration)
    {
        $database = $configuration->getConfigKey('database');
        $capsule = $this->capsuleProvider->getCapsule();
        $connection = $capsule->getConnection();

        $tables = $connection->select('SHOW TABLES');

        $this->logger->warning(sprintf('Zonk Performing Opertations on %s', $database['database']));
        $this->logger->info(sprintf('Number of tables: %d', count($tables)));
    }
}
