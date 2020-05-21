<?php

declare(strict_types=1);

/**
 * User module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-user
 * @package   Mailery\User
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

namespace Mailery\User\Entity;

use Yiisoft\Auth\IdentityInterface;
use Mailery\Common\Entity\RoutableEntityInterface;
use Mailery\Activity\Log\Entity\LoggableEntityTrait;
use Mailery\Activity\Log\Entity\LoggableEntityInterface;

/**
 * @Cycle\Annotated\Annotation\Entity(
 *      table = "users",
 *      repository = "Mailery\User\Repository\UserRepository",
 *      mapper = "Mailery\User\Mapper\DefaultMapper"
 * )
 * @Cycle\Annotated\Annotation\Table(
 *      indexes = {
 *          @Cycle\Annotated\Annotation\Table\Index(columns = {"email"}, unique = true),
 *          @Cycle\Annotated\Annotation\Table\Index(columns = {"username"}, unique = true),
 *          @Cycle\Annotated\Annotation\Table\Index(columns = {"status"})
 *      }
 * )
 */
class User implements IdentityInterface, RoutableEntityInterface, LoggableEntityInterface
{
    use LoggableEntityTrait;

    const STATUS_ACTIVE = 'active';
    const STATUS_DISABLED = 'disabled';

    const PASSWORD_RESET_TOKEN_EXPIRE = 3600;

    /**
     * @Cycle\Annotated\Annotation\Column(type = "primary")
     * @var int|null
     */
    private $id;

    /**
     * @Cycle\Annotated\Annotation\Column(type = "string(32)")
     * @var string
     */
    private $email;

    /**
     * @Cycle\Annotated\Annotation\Column(type = "string(32)")
     * @var string
     */
    private $username;

    /**
     * @Cycle\Annotated\Annotation\Column(type = "string(64)", nullable = true)
     */
    private $password;

    /**
     * @Cycle\Annotated\Annotation\Column(type = "enum(active, disabled)", default = "active")
     */
    private $status = 'active';

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getUsername();
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id ? (string) $this->id : null;
    }

    /**
     * @param int $id
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return self
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return self
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEditRouteName(): ?string
    {
        return '/user/default/edit';
    }

    /**
     * @inheritdoc
     */
    public function getEditRouteParams(): array
    {
        return ['id' => $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getViewRouteName(): ?string
    {
        return '/user/default/view';
    }

    /**
     * @inheritdoc
     */
    public function getViewRouteParams(): array
    {
        return ['id' => $this->getId()];
    }
}
