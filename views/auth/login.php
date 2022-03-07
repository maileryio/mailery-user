<?php

use Yiisoft\Html\Html;
use Yiisoft\Form\Widget\Form;

/** @var Yiisoft\Form\Widget\Field $field */
/** @var Yiisoft\View\WebView $this */
/** @var Mailery\User\Form\UserForm $form */
/** @var string $csrf */

?>

<div class="mb-4"></div>
<div class="row">
    <div class="col-6 offset-3">
        <?= Form::widget()
            ->options(
                [
                    'id' => 'form-login',
                    'csrf' => $csrf,
                    'enctype' => 'multipart/form-data',
                ]
            )
            ->begin(); ?>

        <?= $field->config($form, 'login'); ?>

        <?= $field->config($form, 'password')
                ->passwordInput();
        ?>

        <?= Html::submitButton(
            'Login',
            [
                'class' => 'btn btn-primary float-right mt-2',
            ]
        ); ?>

        <?= Form::end(); ?>
    </div>
</div>
