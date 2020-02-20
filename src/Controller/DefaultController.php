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
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Cycle\ORM\ORMInterface;
use Mailery\User\Repository\UserRepository;
use Mailery\User\Entity\User;
use Mailery\User\Form\UserForm;
use Yiisoft\Data\Reader\Sort;
use Mailery\Dataview\Paginator\OffsetPaginator;
use Yiisoft\Router\UrlGeneratorInterface as UrlGenerator;
use Yiisoft\Http\Method;
use Cycle\ORM\Transaction;

class DefaultController extends Controller
{
    private const PAGINATION_INDEX = 5;

    /**
     * @param Request $request
     * @param ORMInterface $orm
     * @return Response
     */
    public function index(Request $request, ORMInterface $orm): Response
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

    /**
     * @param Request $request
     * @return Response
     */
    public function view(Request $request, ORMInterface $orm): Response
    {
        /** @var UserRepository $userRepo */
        $userRepo = $orm->getRepository(User::class);

        $userId = $request->getAttribute('id');
        if (empty($userId) || ($user = $userRepo->findByPK($userId)) === null) {
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
            ]);

        $submitted = $request->getMethod() === Method::POST;

        if ($submitted) {
            $userForm->loadFromServerRequest($request);

            if ($userForm->isValid() && ($user = $userForm->save()) !== null) {
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
        /** @var UserRepository $userRepo */
        $userRepo = $orm->getRepository(User::class);

        $userId = $request->getAttribute('id');
        if (empty($userId) || ($user = $userRepo->findByPK($userId)) === null) {
            return $this->getResponseFactory()->createResponse(404);
        }

        $userForm
            ->setAttributes([
                'action' => $request->getUri()->getPath(),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ])
            ->withUser($user);

        $submitted = $request->getMethod() === Method::POST;

        if ($submitted) {
            $userForm->loadFromServerRequest($request);

            if ($userForm->isValid() && ($user = $userForm->save()) !== null) {
                return $this->redirect($urlGenerator->generate('/user/default/view', ['id' => $user->getId()]));
            }
        }

        return $this->render('edit', compact('user', 'userForm', 'submitted'));
    }

    /**
     * @param Request $request
     * @param ORMInterface $orm
     * @param UrlGenerator $urlGenerator
     * @return Response
     */
    public function delete(Request $request, ORMInterface $orm, UrlGenerator $urlGenerator): Response
    {
        /** @var UserRepository $userRepo */
        $userRepo = $orm->getRepository(User::class);

        $userId = $request->getAttribute('id');
        if (empty($userId) || ($user = $userRepo->findByPK($userId)) === null) {
            return $this->getResponseFactory()->createResponse(404);
        }

        $tr = new Transaction($orm);
        $tr->delete($user);
        $tr->run();

        return $this->redirect($urlGenerator->generate('/user/default/index'));
    }
}
