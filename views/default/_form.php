<?php

use Mailery\Widget\Select\Select;
use Yiisoft\Form\Widget\Form;

/** @var Yiisoft\Form\Widget\Field $field */
/** @var Yiisoft\View\WebView $this */
/** @var Yiisoft\Form\FormModelInterface $form */
/** @var Yiisoft\Yii\View\Csrf $csrf */

?>
<div class="row">
    <div class="col-12 col-xl-4">
        <?= Form::widget()
                ->csrf($csrf)
                ->id('user-form')
                ->begin(); ?>

        <?= $field->email($form, 'email')->autofocus(); ?>

        <?= $field->text($form, 'username'); ?>

        <?= $field->password($form, 'password'); ?>

        <?= $field->password($form, 'confirmPassword'); ?>

        <?= $field->select(
                $form,
                'role',
                [
                    'class' => Select::class,
                    'items()' => [$form->getRoleListOptions()],
                    'clearable()' => [false],
                    'searchable()' => [false],
                ]
            ); ?>

        <?= $field->select(
                $form,
                'status',
                [
                    'class' => Select::class,
                    'items()' => [$form->getStatusListOptions()],
                    'clearable()' => [false],
                    'searchable()' => [false],
                ]
            ); ?>

        <?= $field->submitButton()
                ->class('btn btn-primary float-right mt-2')
                ->value('Save'); ?>

        <?= Form::end(); ?>
    </div>
</div>