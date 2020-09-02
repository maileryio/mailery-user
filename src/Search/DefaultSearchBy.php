<?php

declare(strict_types=1);

/**
 * User module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-user
 * @package   Mailery\User
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

namespace Mailery\User\Search;

use Mailery\Widget\Search\Model\SearchBy;

class DefaultSearchBy extends SearchBy
{
    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return [
            self::getOperator(),
            [
                ['like', 'email', '%' . $this->getSearchPhrase() . '%'],
                ['like', 'username', '%' . $this->getSearchPhrase() . '%'],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getOperator(): string
    {
        return 'or';
    }
}
