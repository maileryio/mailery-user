<?php

namespace Mailery\User\Search;

use Cycle\ORM\Select;
use Cycle\ORM\Select\QueryBuilder;
use Mailery\Widget\Search\Model\SearchBy;

class DefaultSearchBy extends SearchBy
{
    /**
     * @inheritdoc
     */
    protected function buildQueryInternal(Select $query, string $searchPhrase): Select
    {
        $newQuery = clone $query;

        $newQuery->andWhere(function(QueryBuilder $select) use($searchPhrase) {
            return $select
                ->andWhere(['email' => ['like' => '%' . $searchPhrase . '%']])
                ->orWhere(['username' => ['like' => '%' . $searchPhrase . '%']]);
        });
        return $newQuery;
    }
}