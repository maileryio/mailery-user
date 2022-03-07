<?php
declare(strict_types=1);

namespace Mailery\User\Controller;

use Mailery\User\Form\LoginForm;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Http\Status;
use Yiisoft\Http\Header;
use Yiisoft\Router\UrlGeneratorInterface as UrlGenerator;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\User\CurrentUser;
use Yiisoft\Validator\ValidatorInterface;

class AuthController
{
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
     * @var CurrentUser
     */
    private CurrentUser $currentUser;

    /**
     * @param ViewRenderer $viewRenderer
     * @param ResponseFactory $responseFactory
     * @param UrlGenerator $urlGenerator
     * @param CurrentUser $currentUser
     */
    public function __construct(
        ViewRenderer $viewRenderer,
        ResponseFactory $responseFactory,
        UrlGenerator $urlGenerator,
        CurrentUser $currentUser
    ) {
        $this->viewRenderer = $viewRenderer
            ->withController($this)
            ->withViewPath(dirname(dirname(__DIR__)) . '/views')
            ->withLayout('@views/layout/guest');

        $this->responseFactory = $responseFactory;
        $this->urlGenerator = $urlGenerator;
        $this->currentUser = $currentUser;
    }

    /**
     * @param Request $request
     * @patram ValidatorInterface $validator
     * @param LoginForm $form
     * @return Response
     */
    public function login(Request $request, ValidatorInterface $validator, LoginForm $form): Response
    {
        $body = $request->getParsedBody();

        if (($request->getMethod() === Method::POST) && $form->load($body) && $validator->validate($form)->isValid()) {
            $identity = $form->getIdentity();

            if ($this->currentUser->login($identity)) {
                return $this->responseFactory
                    ->createResponse(Status::FOUND)
                    ->withHeader(Header::LOCATION, $this->urlGenerator->generate('/brand/default/index'));
            }

            $form->addError('login', 'Unable to login.');
        }

        return $this->viewRenderer->render('login', compact('form'));
    }

    /**
     * @return Response
     */
    public function logout(): Response
    {
        $this->currentUser->logout();

        return $this->responseFactory
            ->createResponse(Status::FOUND)
            ->withHeader(Header::LOCATION, $this->urlGenerator->generate('/brand/default/index'));
    }
}
