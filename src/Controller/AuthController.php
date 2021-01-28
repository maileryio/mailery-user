<?php
declare(strict_types=1);

namespace Mailery\User\Controller;

use Mailery\User\Form\LoginForm;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Yiisoft\Http\Method;
use Yiisoft\Router\UrlGeneratorInterface as UrlGenerator;
use Yiisoft\Yii\View\ViewRenderer;

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
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param ViewRenderer $viewRenderer
     * @param ResponseFactory $responseFactory
     */
    public function __construct(
        ViewRenderer $viewRenderer,
        ResponseFactory $responseFactory
    ) {
        $this->viewRenderer = $viewRenderer
            ->withController($this)
            ->withViewBasePath(dirname(dirname(__DIR__)) . '/views')
            ->withLayout('@views/layout/guest');

        $this->responseFactory = $responseFactory;
    }

    /**
     * @param Request $request
     * @param LoginForm $loginForm
     * @param UrlGenerator $urlGenerator
     * @return Response
     */
    public function login(Request $request, LoginForm $loginForm, UrlGenerator $urlGenerator): Response
    {
        $loginForm
            ->setAttributes([
                'action' => $request->getUri()->getPath(),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ])
        ;

        $submitted = $request->getMethod() === Method::POST;

        if ($submitted) {
            $loginForm->loadFromServerRequest($request);

            if (($user = $loginForm->login()) !== null) {
                return $this->responseFactory
                    ->createResponse(302)
                    ->withHeader('Location', $urlGenerator->generate('/brand/default/index'));
            }
        }

        return $this->viewRenderer->render('login', compact('loginForm', 'submitted'));
    }

    /**
     * @return Response
     */
    public function logout(): Response
    {
        $this->user->logout();

        return $this->responseFactory
            ->createResponse(302)
            ->withHeader(
                'Location',
                $this->urlGenerator->generate('/brand/default/index')
            );
    }
}
