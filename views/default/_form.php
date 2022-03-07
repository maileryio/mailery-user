<?php

use Yiisoft\Html\Html;
use Yiisoft\Form\Widget\Form;

/** @var Yiisoft\Form\Widget\Field $field */
/** @var Yiisoft\View\WebView $this */
/** @var Mailery\User\Form\UserForm $form */
/** @var string $csrf */

?>
<div class="row">
    <div class="col-12 col-xl-4">
        <?= Form::widget()
            ->options(
                [
                    'id' => 'form-user',
                    'csrf' => $csrf,
                    'enctype' => 'multipart/form-data',
                ]
            )
            ->begin(); ?>

        <?= $field->config($form, 'email'); ?>

        <?= $field->config($form, 'username'); ?>

        <?= $field->config($form, 'password')
                ->passwordInput();
        ?>

        <?= $field->config($form, 'confirmPassword')
                ->passwordInput();
        ?>

        <?= $field->config($form, 'role')
                ->dropDownList($form->getRoleListOptions());
        ?>

        <?= $field->config($form, 'status')
                ->dropDownList($form->getStatusListOptions());
        ?>

        <?= Html::submitButton(
            'Save',
            [
                'class' => 'btn btn-primary float-right mt-2',
            ]
        ); ?>

        <?= Form::end(); ?>
    </div>
</div>