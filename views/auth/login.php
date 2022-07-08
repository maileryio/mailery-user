<?php

use Yiisoft\Html\Tag\Form;
use Yiisoft\Form\Field;

/** @var Yiisoft\View\WebView $this */
/** @var Yiisoft\Form\FormModelInterface $form */
/** @var Yiisoft\Yii\View\Csrf $csrf */

?>

<div class="mb-4"></div>
<div class="row">
    <div class="col-6 offset-3">
        <div class="card mb-3">
            <div class="card-body">
                <?= Form::tag()
                        ->csrf($csrf)
                        ->id('login-form')
                        ->post()
                        ->open(); ?>

                <?= Field::text($form, 'login')->autofocus(); ?>

                <?= Field::password($form, 'password'); ?>

                <?= Field::submitButton()
                        ->content('Login'); ?>

                <?= Form::tag()->close(); ?>
            </div>
        </div>
    </div>
</div>
