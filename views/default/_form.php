<?php

use Mailery\Widget\Select\Select;
use Yiisoft\Form\Widget\Form;

/** @var Yiisoft\Form\Widget\Field $field */
/** @var Yiisoft\View\WebView $this */
/** @var Mailery\User\Form\UserForm $form */
/** @var Yiisoft\Yii\View\Csrf $csrf */

?>
<div class="row">
    <div class="col-12">
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
                'roles',
                [
                    'class' => Select::class,
                    'items()' => [$form->getRoleListOptions()],
                    'multiple()' => [true],
                    'taggable()' => [true],
                    'searchable()' => [false],
                    'clearable()' => [false],
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

        <?= $field->select(
                $form,
                'country',
                [
                    'class' => Select::class,
                    'items()' => [$form->getCountryListOptions()],
                    'clearable()' => [false],
                    'searchable()' => [true],
                ]
            ); ?>

        <?= $field->select(
                $form,
                'timezone',
                [
                    'class' => Select::class,
                    'items()' => [$form->getTimezoneListOptions()],
                    'clearable()' => [false],
                    'searchable()' => [true],
                ]
            ); ?>

        <?= $field->submitButton()
                ->class('btn btn-primary float-right mt-2')
                ->value($form->hasEntity() ? 'Save changes' : 'Add user'); ?>

        <?= Form::end(); ?>
    </div>
</div>