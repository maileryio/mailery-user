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
use Yiisoft\Yii\Web\User\User as WebUser;
use Yiisoft\Auth\IdentityInterface as User;
use Yiisoft\Data\Reader\Filter\FilterInterface;

class UserService
{
    /**
     * @var User
     */
    private User $user;

    /**
     * WebUser $webUser
     */
    private WebUser $webUser;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepo;

    /**
     * @param WebUser $webUser
     * @param UserRepository $userRepo
     */
    public function __construct(WebUser $webUser, UserRepository $userRepo)
    {
        $this->webUser = $webUser;
        $this->userRepo = $userRepo;
    }

    /**
     * @return User
     */
    public function getCurrentUser(): User
    {
        if (($user = $this->webUser->getIdentity()) !== null) {
            $this->user = $user;
        }

        /* @TODO: temporary hack, move to settings module */
        if (empty($this->user) || !$this->user->getId()) {
            $this->user = $this->userRepo->findOne();
        }

        if (!$this->user instanceof User) {
            throw new \RuntimeException('Invalid current user detection');
        }
        return $this->user;
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
     * @param FilterInterface|null $filter
     * @return PaginatorInterface
     */
    public function getFullPaginator(FilterInterface $filter = null): PaginatorInterface
    {
        $dataReader = $this->userRepo
            ->getDataReader();

        if ($filter !== null) {
            $dataReader = $dataReader->withFilter($filter);
        }

        return new OffsetPaginator(
            $dataReader->withSort(
                (new Sort([]))->withOrder(['id' => 'DESC'])
            )
        );
    }
}
