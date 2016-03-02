<?php

namespace Zonk\Operations;

use Psr\Log\LoggerInterface;
use Zonk\Configuration;
use Zonk\Database\Common\DisabledForeignKeyConstraintsTrait;
use Zonk\Database\Common\ListTableNamesTrait;
use Zonk\Database\ConnectionProvider;

class Truncate implements OperationInterface
{
    use DisabledForeignKeyConstraintsTrait;
    use ListTableNamesTrait;

    /** @var ConnectionProvider */
    protected $connectionProvider;

    /** @var LoggerInterface */
    private $logger;

    /**
     * Truncate constructor.
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

        if (!isset($operations['truncate'])) {
            return true;
        }

        $tables = $this->getListTableNames($this->connectionProvider);

        $this->connectionProvider->getConnection()->beginTransaction();

        $this->doDisabledForeignKeyConstraints(
            $this->connectionProvider,
            function () use ($operations, $tables) {
                foreach ($operations['truncate'] as $table) {
                    if ($this->hasWildcard($table)) {
                        $this->truncateTableWithWildcard($tables, $table);
                        continue;
                    }

                    $this->truncateTable($table, $tables);
                }
            }
        );

        $this->connectionProvider->getConnection()->commit();

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

            foreach ($tables as $tableName) {
                if (strstr($tableName, $wildcardTable) !== false) {
                    $this->truncateTable(strstr($tableName, $wildcardTable), $tables);
                }
            }

        }
    }

    /**
     * @param       $tableName
     * @param array $tables
     *
     * @return bool
     */
    private function truncateTable($tableName, array $tables)
    {
        $connection = $this->connectionProvider->getConnection();

        if (!in_array($tableName, $tables)) {
            $this->logger->info(sprintf('No Table, Skipping `%s`', $tableName));

            return true;
        }

        $this->logger->info(sprintf('Truncating %s', $tableName));
        $rs = $connection->query(sprintf('TRUNCATE TABLE `%s`', $tableName));

        return true;
    }
}
