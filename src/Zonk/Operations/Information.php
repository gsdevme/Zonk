<?php

namespace Zonk\Operations;

use Psr\Log\LoggerInterface;
use Zonk\Configuration;
use Zonk\Database\CapsuleProvider;
use Zonk\Database\Common\ShowTablesTrait;

class Information implements OperationInterface
{
    use ShowTablesTrait;

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

        $tables = $this->getShowTables($this->capsuleProvider);

        $this->logger->warning(sprintf('Zonk Performing Opertations on %s', $database['database']));
        $this->logger->info(sprintf('Number of tables: %d', count($tables)));
    }
}
