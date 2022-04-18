<?php

use Yiisoft\Form\Widget\Form;

/** @var Yiisoft\Form\Widget\Field $field */
/** @var Yiisoft\View\WebView $this */
/** @var Yiisoft\Form\FormModelInterface $form */
/** @var Yiisoft\Yii\View\Csrf $csrf */

?>

<div class="mb-4"></div>
<div class="row">
    <div class="col-6 offset-3">
        <?= Form::widget()
                ->csrf($csrf)
                ->id('login-form')
                ->begin(); ?>

        <?= $field->text($form, 'login')->autofocus(); ?>

        <?= $field->password($form, 'password'); ?>

        <?= $field->submitButton()
                ->class('btn btn-primary float-right mt-2')
                ->value('Login'); ?>

        <?= Form::end(); ?>
    </div>
</div>
