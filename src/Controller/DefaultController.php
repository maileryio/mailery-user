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

use Cycle\ORM\ORMInterface;
use Mailery\User\Controller;
use Mailery\User\Entity\User;
use Mailery\User\Form\UserForm;
use Mailery\User\Repository\UserRepository;
use Mailery\Widget\Dataview\Paginator\OffsetPaginator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Http\Method;
use Yiisoft\Router\UrlGeneratorInterface as UrlGenerator;

use Mailery\Widget\Search\Form\SearchForm;
use Mailery\User\Service\UserService;
use Mailery\Widget\Search\Model\SearchByList;
use Mailery\User\Search\DefaultSearchBy;
use Mailery\Widget\Search\Data\Reader\Search;

class DefaultController extends Controller
{
    private const PAGINATION_INDEX = 10;

    /**
     * @param Request $request
     * @param ORMInterface $orm
     * @param SearchForm $searchForm
     * @return Response
     */
    public function index(Request $request, ORMInterface $orm, SearchForm $searchForm): Response
    {
        $searchForm = $searchForm->withSearchByList(new SearchByList([
            new DefaultSearchBy()
        ]));

        $queryParams = $request->getQueryParams();
        $pageNum = (int) ($queryParams['page'] ?? 1);

        $dataReader = $this->getUserRepository($orm)
            ->getDataReader()
            ->withSearch((new Search())->withSearchPhrase($searchForm->getSearchPhrase())->withSearchBy($searchForm->getSearchBy()))
            ->withSort((new Sort([]))->withOrderString('username'));

        $paginator = (new OffsetPaginator($dataReader))
            ->withPageSize(self::PAGINATION_INDEX)
            ->withCurrentPage($pageNum);

        return $this->render('index', compact('searchForm', 'paginator'));
    }

    /**
     * @param Request $request
     * @param ORMInterface $orm
     * @return Response
     */
    public function view(Request $request, ORMInterface $orm): Response
    {
        $userId = $request->getAttribute('id');
        if (empty($userId) || ($user = $this->getUserRepository($orm)->findByPK($userId)) === null) {
            return $this->getResponseFactory()->createResponse(404);
        }

        return $this->render('view', compact('user'));
    }

    /**
     * @param Request $request
     * @param UserForm $userForm
     * @param UrlGenerator $urlGenerator
     * @return Response
     */
    public function create(Request $request, UserForm $userForm, UrlGenerator $urlGenerator): Response
    {
        $userForm
            ->setAttributes([
                'action' => $request->getUri()->getPath(),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ])
        ;

        $submitted = $request->getMethod() === Method::POST;

        if ($submitted) {
            $userForm->loadFromServerRequest($request);

            if (($user = $userForm->save()) !== null) {
                return $this->redirect($urlGenerator->generate('/user/default/view', ['id' => $user->getId()]));
            }
        }

        return $this->render('create', compact('userForm', 'submitted'));
    }

    /**
     * @param Request $request
     * @param ORMInterface $orm
     * @param UserForm $userForm
     * @param UrlGenerator $urlGenerator
     * @return Response
     */
    public function edit(Request $request, ORMInterface $orm, UserForm $userForm, UrlGenerator $urlGenerator): Response
    {
        $userId = $request->getAttribute('id');
        if (empty($userId) || ($user = $this->getUserRepository($orm)->findByPK($userId)) === null) {
            return $this->getResponseFactory()->createResponse(404);
        }

        $userForm
            ->withUser($user)
            ->setAttributes([
                'action' => $request->getUri()->getPath(),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ])
        ;

        $submitted = $request->getMethod() === Method::POST;

        if ($submitted) {
            $userForm->loadFromServerRequest($request);

            if ($userForm->save() !== null) {
                return $this->redirect($urlGenerator->generate('/user/default/view', ['id' => $user->getId()]));
            }
        }

        return $this->render('edit', compact('user', 'userForm', 'submitted'));
    }

    /**
     * @param Request $request
     * @param ORMInterface $orm
     * @param UserService $userService
     * @param UrlGenerator $urlGenerator
     * @return Response
     */
    public function delete(Request $request, ORMInterface $orm, UserService $userService, UrlGenerator $urlGenerator): Response
    {
        $userId = $request->getAttribute('id');
        if (empty($userId) || ($user = $this->getUserRepository($orm)->findByPK($userId)) === null) {
            return $this->getResponseFactory()->createResponse(404);
        }

        $userService->delete($user);

        return $this->redirect($urlGenerator->generate('/user/default/index'));
    }

    /**
     * @param ORMInterface $orm
     * @return UserRepository
     */
    private function getUserRepository(ORMInterface $orm): UserRepository
    {
        return $orm->getRepository(User::class);
    }
}
