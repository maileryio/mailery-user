<?php

use Mailery\Widget\Form\FormRenderer;

/** @var Mailery\Web\View\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var FormManager\Form $userForm */
/** @var bool $submitted */

$this->setTitle('New User');

?><div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2">New User</h1>
            <div class="btn-toolbar float-right">
                <a class="btn btn-sm btn-outline-secondary mx-sm-1 mb-2" href="<?= $urlGenerator->generate('/user/default/index') ?>">
                    Back
                </a>
            </div>
        </div>
    </div>
</div>
<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <?= (new FormRenderer())($userForm, $submitted) ?>
    </div>
</div>
