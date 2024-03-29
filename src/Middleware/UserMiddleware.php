<?php

namespace Mailery\User\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;
use Yiisoft\User\CurrentUser;
use Mailery\User\Entity\User as UserEntity;
use Mailery\User\Service\CurrentUserService;

class UserMiddleware implements MiddlewareInterface
{
    /**
     * @param CurrentUser $user
     * @param CurrentUserService $currentUser
     */
    public function __construct(
        private CurrentUser $user,
        private CurrentUserService $currentUser
    ) {}

    /**
     * @param Request $request
     * @param Handler $handler
     * @return Response
     */
    public function process(Request $request, Handler $handler): Response
    {
        if (($identity = $this->user->getIdentity()) instanceof UserEntity) {
            $this->currentUser->setUser($identity);
        }

        return $handler->handle($request);
    }
}
