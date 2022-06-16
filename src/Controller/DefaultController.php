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
use Yiisoft\Http\Status;
use Yiisoft\Http\Header;
use Yiisoft\Router\UrlGeneratorInterface as UrlGenerator;
use Yiisoft\Yii\View\ViewRenderer;
use Mailery\User\Repository\UserRepository;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Mailery\User\ValueObject\UserValueObject;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Rbac\Manager;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\User\CurrentUser;

class DefaultController
{
    private const PAGINATION_INDEX = 10;

    /**
     * @param ViewRenderer $viewRenderer
     * @param ResponseFactory $responseFactory
     * @param UrlGenerator $urlGenerator
     * @param UserRepository $userRepo
     * @param UserService $userService
     * @param UserCrudService $userCrudService
     */
    public function __construct(
        private ViewRenderer $viewRenderer,
        private ResponseFactory $responseFactory,
        private UrlGenerator $urlGenerator,
        private UserRepository $userRepo,
        private UserService $userService,
        private UserCrudService $userCrudService
    ) {
        $this->viewRenderer = $viewRenderer
            ->withController($this)
            ->withViewPath(dirname(dirname(__DIR__)) . '/views');
    }

    /**
     * @param Request $request
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
     * @param CurrentRoute $currentRoute
     * @param Manager $manager
     * @return Response
     */
    public function view(CurrentRoute $currentRoute, Manager $manager): Response
    {
        $userId = $currentRoute->getArgument('id');
        if (empty($userId) || ($user = $this->userRepo->findByPK($userId)) === null) {
            return $this->responseFactory->createResponse(Status::NOT_FOUND);
        }

        return $this->viewRenderer->render('view', compact('user', 'manager'));
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param UserForm $form
     * @return Response
     */
    public function create(Request $request, ValidatorInterface $validator, UserForm $form): Response
    {
        $body = $request->getParsedBody();

        if (($request->getMethod() === Method::POST) && $form->load($body) && $validator->validate($form)->isValid()) {
            $valueObject = UserValueObject::fromForm($form);
            $user = $this->userCrudService->create($valueObject);

            return $this->responseFactory
                ->createResponse(Status::FOUND)
                ->withHeader(Header::LOCATION, $this->urlGenerator->generate('/user/default/view', ['id' => $user->getId()]));
        }

        return $this->viewRenderer->render('create', compact('form'));
    }

    /**
     * @param Request $request
     * @param CurrentRoute $currentRoute
     * @param ValidatorInterface $validator
     * @param FlashInterface $flash
     * @param UserForm $form
     * @return Response
     */
    public function edit(Request $request, CurrentRoute $currentRoute, ValidatorInterface $validator, FlashInterface $flash, UserForm $form): Response
    {
        $body = $request->getParsedBody();
        $userId = $currentRoute->getArgument('id');
        if (empty($userId) || ($user = $this->userRepo->findByPK($userId)) === null) {
            return $this->responseFactory->createResponse(Status::NOT_FOUND);
        }

        $form = $form->withEntity($user);

        if (($request->getMethod() === Method::POST) && $form->load($body) && $validator->validate($form)->isValid()) {
            $valueObject = UserValueObject::fromForm($form);
            $this->userCrudService->update($user, $valueObject);

            $flash->add(
                'success',
                [
                    'body' => 'Data have been saved!',
                ],
                true
            );
        }

        return $this->viewRenderer->render('edit', compact('form', 'user'));
    }

    /**
     * @param CurrentRoute $currentRoute
     * @param CurrentUser $currentUser
     * @return Response
     */
    public function delete(CurrentRoute $currentRoute, CurrentUser $currentUser): Response
    {
        $userId = $currentRoute->getArgument('id');
        if (empty($userId) || ($user = $this->userRepo->findByPK($userId)) === null) {
            return $this->responseFactory->createResponse(Status::NOT_FOUND);
        }

        if ($currentUser->getId() === $user->getId()) {
            return $this->responseFactory->createResponse(Status::FORBIDDEN);
        }

        $this->userCrudService->delete($user);

        return $this->responseFactory
            ->createResponse(Status::SEE_OTHER)
            ->withHeader(Header::LOCATION, $this->urlGenerator->generate('/user/default/index'));
    }
}
