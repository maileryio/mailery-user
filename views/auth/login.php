<?php

use Mailery\Widget\Form\FormRenderer;

/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var FormManager\Form $userForm */
/** @var string $csrf */
/** @var bool $submitted */

?><div class="mb-4"></div>
<div class="row">
    <div class="col-6 offset-3">
        <?= (new FormRenderer($loginForm->withCsrf($csrf)))($submitted); ?>
    </div>
</div>
