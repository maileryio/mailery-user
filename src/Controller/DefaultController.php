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

use Mailery\Web\Controller;
use Psr\Http\Message\ResponseInterface;

class DefaultController extends Controller
{
    /**
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return $this->render('index');
    }
}
