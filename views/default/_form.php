<?php

use Mailery\Widget\Select\Select;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Form\Field;

/** @var Yiisoft\View\WebView $this */
/** @var Mailery\User\Form\UserForm $form */
/** @var Yiisoft\Yii\View\Csrf $csrf */

?>
<div class="row">
    <div class="col-12">
        <?= Form::tag()
                ->csrf($csrf)
                ->id('user-form')
                ->post()
                ->open(); ?>

        <?= Field::email($form, 'email')->autofocus(); ?>

        <?= Field::text($form, 'username'); ?>

        <?= Field::password($form, 'password'); ?>

        <?= Field::password($form, 'confirmPassword'); ?>

        <?= Field::input(
                Select::class,
                $form,
                'roles',
                [
                    'optionsData()' => [$form->getRoleListOptions()],
                    'multiple()' => [true],
                    'taggable()' => [true],
                    'searchable()' => [false],
                    'clearable()' => [false],
                ]
            ); ?>

        <?= Field::input(
                Select::class,
                $form,
                'status',
                [
                    'optionsData()' => [$form->getStatusListOptions()],
                    'clearable()' => [false],
                    'searchable()' => [false],
                ]
            ); ?>

        <?= Field::input(
                Select::class,
                $form,
                'country',
                [
                    'optionsData()' => [$form->getCountryListOptions()],
                    'clearable()' => [false],
                    'searchable()' => [true],
                ]
            ); ?>

        <?= Field::input(
                Select::class,
                $form,
                'timezone',
                [
                    'optionsData()' => [$form->getTimezoneListOptions()],
                    'clearable()' => [false],
                    'searchable()' => [true],
                ]
            ); ?>

        <?= Field::submitButton()
                ->content($form->hasEntity() ? 'Save changes' : 'Add user'); ?>

        <?= Form::tag()->close(); ?>
    </div>
</div>