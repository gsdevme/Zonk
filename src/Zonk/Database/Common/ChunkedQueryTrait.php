<?php

namespace Zonk\Database\Common;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Class ChunkedQueryTrait
 *
 * @package Zonk\Database\Common
 */
trait ChunkedQueryTrait
{
    /**
     * @param QueryBuilder  $queryBuilder
     * @param callable      $callable
     * @param int           $limit
     * @param int           $offset
     * @param callable|null $outerCallable
     *
     * @return mixed
     */
    public function chunkedQuery(
        QueryBuilder $queryBuilder,
        callable $callable,
        $limit = 30,
        $offset = 0,
        callable $outerCallable = null
    ) {
        $rows = $queryBuilder->setMaxResults($limit)
            ->setFirstResult($offset)
            ->execute();

        if ($rows->rowCount() <= 0) {
            return;
        }

        foreach ($rows as $row) {
            call_user_func_array($callable, [$row, $queryBuilder->getConnection()]);
        }

        call_user_func($outerCallable, $offset + $rows->rowCount());

        unset($rows);

        $offset += $limit;

        return $this->chunkedQuery($queryBuilder, $callable, $limit, $offset, $outerCallable);
    }
}
