<?php

namespace Mailery\User\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;
use Yiisoft\User\User;
use Mailery\User\Entity\User as UserEntity;
use Mailery\User\Service\CurrentUserService;

class CurrentUserMiddleware implements MiddlewareInterface
{
    /**
     * @var User
     */
    private User $user;

    /**
     * @var CurrentUserService
     */
    private CurrentUserService $currentUser;

    /**
     * @param User $user
     * @param CurrentUserService $currentUser
     */
    public function __construct(User $user, CurrentUserService $currentUser)
    {
        $this->user = $user;
        $this->currentUser = $currentUser;
    }

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
