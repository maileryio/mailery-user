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

use Mailery\User\Form\UserForm;
use Mailery\User\Service\UserService;
use Mailery\User\Service\UserCrudService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Router\UrlGeneratorInterface as UrlGenerator;
use Yiisoft\Yii\View\ViewRenderer;
use Mailery\User\Repository\UserRepository;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;

class DefaultController
{
    private const PAGINATION_INDEX = 10;

    /**
     * @var ViewRenderer
     */
    private ViewRenderer $viewRenderer;

    /**
     * @var ResponseFactory
     */
    private ResponseFactory $responseFactory;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepo;

    /**
     * @var UserService
     */
    private UserService $userService;

    /**
     * @param ViewRenderer $viewRenderer
     * @param ResponseFactory $responseFactory
     * @param UserRepository $userRepo
     * @param UserService $userService
     */
    public function __construct(
        ViewRenderer $viewRenderer,
        ResponseFactory $responseFactory,
        UserRepository $userRepo,
        UserService $userService
    ) {
        $this->viewRenderer = $viewRenderer
            ->withController($this)
            ->withViewBasePath(dirname(dirname(__DIR__)) . '/views');

        $this->responseFactory = $responseFactory;
        $this->userRepo = $userRepo;
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @param UserService $userService
     * @return Response
     */
    public function index(Request $request): Response
    {
        $queryParams = $request->getQueryParams();
        $pageNum = (int) ($queryParams['page'] ?? 1);
        $searchBy = $queryParams['searchBy'] ?? null;
        $searchPhrase = $queryParams['search'] ?? null;

        $searchForm = $this->userService->getSearchForm()
            ->withSearchBy($searchBy)
            ->withSearchPhrase($searchPhrase);

        $paginator = $this->userService->getFullPaginator($searchForm->getSearchBy())
            ->withPageSize(self::PAGINATION_INDEX)
            ->withCurrentPage($pageNum);

        return $this->viewRenderer->render('index', compact('searchForm', 'paginator'));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function view(Request $request): Response
    {
        $userId = $request->getAttribute('id');
        if (empty($userId) || ($user = $this->userRepo->findByPK($userId)) === null) {
            return $this->responseFactory->createResponse(404);
        }

        return $this->viewRenderer->render('view', compact('user'));
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
                return $this->responseFactory
                    ->createResponse(302)
                    ->withHeader('Location', $urlGenerator->generate('/user/default/view', ['id' => $user->getId()]));
            }
        }

        return $this->viewRenderer->render('create', compact('userForm', 'submitted'));
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
        if (empty($userId) || ($user = $this->userRepo->findByPK($userId)) === null) {
            return $this->responseFactory->createResponse(404);
        }

        $userForm = $userForm
            ->setAttributes([
                'action' => $request->getUri()->getPath(),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ])
            ->withUser($user)
        ;

        $submitted = $request->getMethod() === Method::POST;

        if ($submitted) {
            $userForm->loadFromServerRequest($request);

            if ($userForm->save() !== null) {
                return $this->responseFactory
                    ->createResponse(302)
                    ->withHeader('Location', $urlGenerator->generate('/user/default/view', ['id' => $user->getId()]));
            }
        }

        return $this->viewRenderer->render('edit', compact('user', 'userForm', 'submitted'));
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
        if (empty($userId) || ($user = $this->userRepo->findByPK($userId)) === null) {
            return $this->responseFactory->createResponse(404);
        }

        $userCrudService->delete($user);

        return $this->responseFactory
            ->createResponse(302)
            ->withHeader('Location', $urlGenerator->generate('/user/default/index'));
    }
}
