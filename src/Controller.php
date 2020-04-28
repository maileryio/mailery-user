<?php

declare(strict_types=1);

/**
 * User module for Mailery Platform
 * @link      https://github.com/maileryio/mailery-user
 * @package   Mailery\User
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

namespace Mailery\User;

use Mailery\Web\Controller as WebController;
use Psr\Http\Message\ResponseFactoryInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\View\WebView;

abstract class Controller extends WebController
{
    /**
     * @param ResponseFactoryInterface $responseFactory
     * @param Aliases $aliases
     * @param WebView $view
     */
    public function __construct(ResponseFactoryInterface $responseFactory, Aliases $aliases, WebView $view)
    {
        parent::__construct($responseFactory, $aliases, $view);

        $this->setBaseViewPath(dirname(__DIR__) . '/views');
    }
}
