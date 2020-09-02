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

use Mailery\Common\Web\Controller;
use Mailery\User\Entity\User;
use Mailery\User\Form\UserForm;
use Mailery\User\Repository\UserRepository;
use Mailery\User\Service\UserService;
use Mailery\User\Service\UserCrudService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Router\UrlGeneratorInterface as UrlGenerator;

class DefaultController extends Controller
{
    private const PAGINATION_INDEX = 10;

    /**
     * @param Request $request
     * @param UserService $userService
     * @return Response
     */
    public function index(Request $request, UserService $userService): Response
    {
        $queryParams = $request->getQueryParams();
        $pageNum = (int) ($queryParams['page'] ?? 1);
        $searchBy = $queryParams['searchBy'] ?? null;
        $searchPhrase = $queryParams['search'] ?? null;

        $searchForm = $userService->getSearchForm()
            ->withSearchBy($searchBy)
            ->withSearchPhrase($searchPhrase);

        $paginator = $userService->getFullPaginator($searchForm)
            ->withPageSize(self::PAGINATION_INDEX)
            ->withCurrentPage($pageNum);

        return $this->render('index', compact('searchForm', 'paginator'));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function view(Request $request): Response
    {
        $userId = $request->getAttribute('id');
        if (empty($userId) || ($user = $this->getUserRepository()->findByPK($userId)) === null) {
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
     * @param UserForm $userForm
     * @param UrlGenerator $urlGenerator
     * @return Response
     */
    public function edit(Request $request, UserForm $userForm, UrlGenerator $urlGenerator): Response
    {
        $userId = $request->getAttribute('id');
        if (empty($userId) || ($user = $this->getUserRepository()->findByPK($userId)) === null) {
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
     * @param UserCrudService $userCrudService
     * @param UrlGenerator $urlGenerator
     * @return Response
     */
    public function delete(Request $request, UserCrudService $userCrudService, UrlGenerator $urlGenerator): Response
    {
        $userId = $request->getAttribute('id');
        if (empty($userId) || ($user = $this->getUserRepository()->findByPK($userId)) === null) {
            return $this->getResponseFactory()->createResponse(404);
        }

        $userCrudService->delete($user);

        return $this->redirect($urlGenerator->generate('/user/default/index'));
    }

    /**
     * @return UserRepository
     */
    private function getUserRepository(): UserRepository
    {
        return $this->getOrm()->getRepository(User::class);
    }
}
