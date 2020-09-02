<?php

declare(strict_types=1);

/**
 * User module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-user
 * @package   Mailery\User
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

namespace Mailery\User\Service;

use Mailery\User\Search\DefaultSearchBy;
use Mailery\Widget\Search\Form\SearchForm;
use Mailery\Widget\Search\Model\SearchByList;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Mailery\User\Repository\UserRepository;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Sort;

class UserService
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepo;

    /**
     * @param UserRepository $userRepo
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * @return SearchForm
     */
    public function getSearchForm(): SearchForm
    {
        return (new SearchForm())
            ->withSearchByList(new SearchByList([
                new DefaultSearchBy(),
            ]));
    }

    /**
     * @param SearchForm $searchForm
     * @return PaginatorInterface
     */
    public function getFullPaginator(SearchForm $searchForm): PaginatorInterface
    {
        $dataReader = $this->userRepo
            ->getDataReader();

        if (($searchBy = $searchForm->getSearchBy()) !== null) {
            $dataReader = $dataReader->withFilter($searchBy);
        }

        return new OffsetPaginator(
            $dataReader->withSort(
                (new Sort([]))->withOrderString('username')
            )
        );
    }
}
