<?php

namespace Zonk\Operations;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Zonk\Configuration;
use Zonk\Database\CapsuleProvider;
use Zonk\Database\ConnectionProvider;
use Zonk\Obfuscation\StrategyRegistry;

class Obfuscate implements OperationInterface
{
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

    public function getName()
    {
        return 'Obfuscate';
    }

    /**
     * @param Configuration $configuration
     *
     * @return bool
     */
    public function doOperation(Configuration $configuration)
    {
//        $operations = $configuration->getConfigKey('operations');
//
//        if (!isset($operations['obfuscate'])
//            && !isset($operations['obfuscate']['tables'])
//            && !isset($operations['obfuscate']['strategies'])
//        ) {
//            return true;
//        }
//
//        $this->addStrategies($operations['obfuscate']['strategies']);
//        $capsule = $this->capsuleProvider->getCapsule();
//
//        foreach ($operations['obfuscate']['tables'] as $tableName => $fields) {
//            $table = $capsule->table($tableName);
//
//            if (!$table->exists()) {
//                $this->logger->info(sprintf('Skipping table %s', $tableName));
//                continue;
//            }
//
//            $count = $table->getCountForPagination();
//            $this->logger->info(sprintf('Table %s has %s rows', $tableName, number_format($count)));
//
//            $table->select(array_keys($fields))->orderBy('id')->each(function ($columns) use (&$table, $fields) {
//                foreach ($fields as $field => $strategy) {
//                    $columns->$field = $this->strategyRegistry->find($strategy)->obfuscate($columns->$field);
//                }
//
//                $this->update((array)$columns);
//
//                //$table->update((array)$columns);
//            }, 5);
//
//            die;
//        }
//
//        return true;
    }

    private function addStrategies($strategies)
    {
        foreach ($strategies as $key => $strategy) {
            $this->strategyRegistry->add($key, $strategy);
        }
    }
}
