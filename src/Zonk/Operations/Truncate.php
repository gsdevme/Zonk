<?php

namespace Zonk\Operations;

use Psr\Log\LoggerInterface;
use Zonk\Configuration;
use Zonk\Database\CapsuleProvider;
use Zonk\Database\DisabledForeignKeyConstraintsTrait;

class Truncate implements OperationInterface
{
    use DisabledForeignKeyConstraintsTrait;

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

        $this->setCapsuleProvider($this->capsuleProvider);
    }

    public function getName()
    {
        return 'Truncate';
    }

    /**
     * @param Configuration $configuration
     *
     * @return bool
     */
    public function doOperation(Configuration $configuration)
    {
        $operations = $configuration->getConfigKey('operations');
        $capsule = $this->capsuleProvider->getCapsule();
        $connection = $capsule->getConnection();

        if (!isset($operations['truncate'])) {
            return true;
        }

        $tables = $connection->select('SHOW TABLES');

        $this->doDisabledForeignKeyConstraints(
            function () use ($operations, $tables) {
                foreach ($operations['truncate'] as $table) {
                    if ($this->hasWildcard($table)) {
                        $this->truncateTableWithWildcard($tables, $table);
                        continue;
                    }

                    $this->truncateTable($table);
                }
            }
        );

        return true;
    }

    /**
     * @param $table
     *
     * @return int
     */
    private function hasWildcard($table)
    {
        return stripos($table, '*');
    }

    /**
     * @param array $tables
     * @param       $table
     */
    private function truncateTableWithWildcard(array $tables, $table)
    {
        if (preg_match('/\*$/', $table, $matches, PREG_OFFSET_CAPTURE)) {
            $matches = array_shift($matches);
            $offset = array_pop($matches);

            $wildcardTable = substr($table, 0, $offset);

            foreach ($tables as $table) {
                $table = array_values((array)$table);
                $tableName = array_shift($table);

                if (strstr($tableName, $wildcardTable) !== false) {
                    $this->truncateTable(strstr($tableName, $wildcardTable));
                }
            }

        }
    }

    /**
     * @param $tableName
     *
     * @return null
     */
    private function truncateTable($tableName)
    {
        $capsule = $this->capsuleProvider->getCapsule();

        if (!$capsule->schema()->hasTable($tableName)) {
            $this->logger->info(sprintf('No Table, Skipping %s', $tableName));

            return true;
        }

        $this->logger->info(sprintf('Truncating %s', $tableName));
        $capsule->table($tableName)->truncate();

        return true;
    }
}
