<?php

namespace Zonk\Operations;

use Psr\Log\LoggerInterface;
use Zonk\ConfigurationInterface;
use Zonk\Database\Common\ChunkedQueryTrait;
use Zonk\Database\Common\CountTableRowsTrait;
use Zonk\Database\Common\ListTableNamesTrait;
use Zonk\Database\Common\PrimaryKeyTrait;
use Zonk\Database\ConnectionProvider;
use Zonk\Obfuscation\StrategyRegistry;

class Obfuscate implements OperationInterface
{
    use ListTableNamesTrait;
    use CountTableRowsTrait;
    use PrimaryKeyTrait;
    use ChunkedQueryTrait;

    /** @var ConnectionProvider */
    protected $connectionProvider;

    /** @var LoggerInterface */
    private $logger;

    /** @var StrategyRegistry */
    private $strategyRegistry;

    /**
     * Obfuscate constructor.
     *
     * @param ConnectionProvider $connectionProvider
     * @param LoggerInterface    $logger
     */
    public function __construct(ConnectionProvider $connectionProvider, LoggerInterface $logger)
    {
        $this->connectionProvider = $connectionProvider;
        $this->logger = $logger;
        $this->strategyRegistry = new StrategyRegistry();
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Obfuscate';
    }

    /**
     * @param ConfigurationInterface $configuration
     *
     * @return bool
     */
    public function doOperation(ConfigurationInterface $configuration)
    {
        $operations = $configuration->getConfigKey('operations');

        if (!isset($operations['obfuscate'])
            && !isset($operations['obfuscate']['tables'])
            && !isset($operations['obfuscate']['strategies'])
        ) {
            return true;
        }

        $this->addStrategies($operations['obfuscate']['strategies']);
        $tables = $this->getListTableNames($this->connectionProvider);

        foreach ($operations['obfuscate']['tables'] as $tableName => $fields) {
            if (!in_array($tableName, $tables)) {
                $this->logger->info(sprintf('Skipping table `%s`', $tableName));
                continue;
            }

            $this->logger->info(sprintf('Obfuscating table `%s`', $tableName));

            $rows = $this->getTableRows($this->connectionProvider, $tableName);
            $this->logger->info(sprintf('Table: `%s` has %d rows', $tableName, $rows));

            $this->doTable($tableName, $fields);
        }
    }

    /**
     * @param $tableName
     * @param $fields
     */
    private function doTable($tableName, $fields)
    {
        $connection = $this->connectionProvider->getConnection();
        $connection->beginTransaction();

        $primaryKey = $this->getPrimaryKey($this->connectionProvider, $tableName);

        $query = $connection->createQueryBuilder()
            ->select(array_merge([$primaryKey], array_keys($fields)))
            ->from($tableName)
            ->orderBy($primaryKey);

        $this->chunkedQuery(
            $query,
            function ($row) use ($fields, $tableName, $connection, $primaryKey) {
                $query = $connection->createQueryBuilder()
                    ->update($tableName)
                    ->where(sprintf('%s = :%s', $primaryKey, $primaryKey))
                    ->setParameter(sprintf(':%s', $primaryKey), $row[$primaryKey]);

                foreach ($fields as $field => $strategy) {
                    $query->set($field, sprintf(':%s', $field));

                    $query->setParameter(
                        sprintf(':%s', $field),
                        $this->strategyRegistry->find($strategy)->obfuscate($row[$field])
                    );
                }

                $connection->executeUpdate(sprintf('%s LIMIT 1', $query->getSQL()), $query->getParameters());

                unset($row);
                unset($query);
            },
            25000,
            0,
            function ($count) use ($tableName, $connection) {
                $this->logger->info(sprintf('Table: %s, obfuscated %s rows', $tableName, number_format($count)));
                $this->logger->warning(sprintf('Memory %s mb', memory_get_usage(true) / 1024 / 1024));
                $connection->commit();
            }
        );
    }

    /**
     * @param $strategies
     */
    private function addStrategies($strategies)
    {
        foreach ($strategies as $key => $strategy) {
            $this->strategyRegistry->add($key, $strategy);
        }
    }
}
