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
     * @var UrlGenerator
     */
    private UrlGenerator $urlGenerator;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepo;

    /**
     * @var UserService
     */
    private UserService $userService;

    /**
     * @var UserCrudService
     */
    private UserCrudService $userCrudService;

    /**
     * @param ViewRenderer $viewRenderer
     * @param ResponseFactory $responseFactory
     * @param UrlGenerator $urlGenerator
     * @param UserRepository $userRepo
     * @param UserService $userService
     * @param UserCrudService $userCrudService
     */
    public function __construct(
        ViewRenderer $viewRenderer,
        ResponseFactory $responseFactory,
        UrlGenerator $urlGenerator,
        UserRepository $userRepo,
        UserService $userService,
        UserCrudService $userCrudService
    ) {
        $this->viewRenderer = $viewRenderer
            ->withController($this)
            ->withViewPath(dirname(dirname(__DIR__)) . '/views');

        $this->responseFactory = $responseFactory;
        $this->urlGenerator = $urlGenerator;
        $this->userRepo = $userRepo;
        $this->userService = $userService;
        $this->userCrudService = $userCrudService;
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
     * @param ValidatorInterface $validator
     * @param FlashInterface $flash
     * @param UserForm $form
     * @return Response
     */
    public function edit(Request $request, ValidatorInterface $validator, FlashInterface $flash, UserForm $form): Response
    {
        $body = $request->getParsedBody();
        $userId = $request->getAttribute('id');
        if (empty($userId) || ($user = $this->userRepo->findByPK($userId)) === null) {
            return $this->responseFactory->createResponse(404);
        }

        $form = $form->withEntity($user);

        if (($request->getMethod() === Method::POST) && $form->load($body) && $validator->validate($form)->isValid()) {
            $valueObject = UserValueObject::fromForm($form);
            $this->userCrudService->update($user, $valueObject);

            return $this->responseFactory
                ->createResponse(Status::FOUND)
                ->withHeader(Header::LOCATION, $this->urlGenerator->generate('/user/default/view', ['id' => $user->getId()]));
        }

        return $this->viewRenderer->render('edit', compact('form', 'user'));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {
        $userId = $request->getAttribute('id');
        if (empty($userId) || ($user = $this->userRepo->findByPK($userId)) === null) {
            return $this->responseFactory->createResponse(404);
        }

        $this->userCrudService->delete($user);

        return $this->responseFactory
            ->createResponse(302)
            ->withHeader(Header::LOCATION, $this->urlGenerator->generate('/user/default/index'));
    }
}
