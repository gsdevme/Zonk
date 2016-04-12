<?php

namespace Zonk\Operations;

use Psr\Log\LoggerInterface;
use Zonk\ConfigurationInterface;
use Zonk\Database\Common\DisabledForeignKeyConstraintsTrait;
use Zonk\Database\Common\ListTableNamesTrait;
use Zonk\Database\ConnectionProvider;

class Delete implements OperationInterface
{
    use DisabledForeignKeyConstraintsTrait;
    use ListTableNamesTrait;

    /** @var ConnectionProvider */
    protected $connectionProvider;

    /** @var LoggerInterface */
    private $logger;

    /**
     * Delete constructor.
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
        return 'Delete';
    }

    /**
     * @param ConfigurationInterface $configuration
     *
     * @return bool
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function doOperation(ConfigurationInterface $configuration)
    {
        $operations = $configuration->getConfigKey('operations');

        if (!isset($operations['delete']) && !isset($operations['delete']['tables'])) {
            return true;
        }

        $tables = $this->getListTableNames($this->connectionProvider);

        $this->connectionProvider->getConnection()->beginTransaction();

        $this->doDisabledForeignKeyConstraints(
            $this->connectionProvider,
            function () use ($operations, $tables) {
                foreach ($operations['delete']['tables'] as $table => $where) {
                    $this->deleteFromTable($table, $where, $tables);
                }
            }
        );

        $this->connectionProvider->getConnection()->commit();

        return true;
    }

    /**
     * @param       $tableName
     * @param       $where
     * @param array $tables
     *
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    private function deleteFromTable($tableName, $where, array $tables)
    {
        $connection = $this->connectionProvider->getConnection();

        if (!in_array($tableName, $tables)) {
            $this->logger->info(sprintf('No Table, Skipping `%s`', $tableName));

            return true;
        }

        $this->logger->info(sprintf('Delete tables in %s where condition %s', $tableName, $where));
        $rs = $connection->query(sprintf('DELETE FROM `%s` WHERE %s', $tableName, $where));

        return true;
    }
}
