<?php

declare(strict_types=1);

/**
 * User module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-user
 * @package   Mailery\User
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

namespace Mailery\User\Controller;

use Mailery\User\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cycle\ORM\ORMInterface;
use Mailery\User\Repository\UserRepository;
use Mailery\User\Entity\User;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Data\Paginator\OffsetPaginator;

class DefaultController extends Controller
{
    private const PAGINATION_INDEX = 5;

    /**
     * @param ServerRequestInterface $request
     * @param ORMInterface $orm
     * @return ResponseInterface
     */
    public function index(ServerRequestInterface $request, ORMInterface $orm): ResponseInterface
    {
        $pageNum = (int) $request->getAttribute('page', 1);
        /** @var UserRepository $userRepo */
        $userRepo = $orm->getRepository(User::class);

        $dataReader = $userRepo->findAll()->withSort((new Sort([]))->withOrderString('username'));
        $paginator = (new OffsetPaginator($dataReader))
            ->withPageSize(self::PAGINATION_INDEX)
            ->withCurrentPage($pageNum);

        return $this->render('index', compact('paginator'));
    }
}
