<?php declare(strict_types=1);

use Mailery\Activity\Log\Widget\ActivityLogLink;
use Mailery\Icon\Icon;
use Mailery\User\Entity\User;
use Mailery\Widget\Dataview\DetailView;
use Mailery\Widget\Link\Link;

/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\User\Entity\User $user */
/** @var Yiisoft\Yii\View\Csrf $csrf */
/** @var bool $submitted */

$this->setTitle($user->getUsername());

?><div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3">
            <h1 class="h3">User #<?= $user->getId(); ?></h1>
            <div class="btn-toolbar float-right">
                <?= Link::widget()
                    ->label(Icon::widget()->name('delete')->options(['class' => 'mr-1'])->render() . ' Delete')
                    ->method('delete')
                    ->href($urlGenerator->generate('/user/default/delete', ['id' => $user->getId()]))
                    ->confirm('Are you sure?')
                    ->options([
                        'class' => 'btn btn-sm btn-danger mx-sm-1 mb-2',
                    ])
                    ->encode(false);
                ?>
                <a class="btn btn-sm btn-secondary mx-sm-1 mb-2" href="<?= $urlGenerator->generate('/user/default/edit', ['id' => $user->getId()]); ?>">
                    <?= Icon::widget()->name('pencil')->options(['class' => 'mr-1']); ?>
                    Update
                </a>
                <b-dropdown right size="sm" variant="secondary" class="mb-2">
                    <template v-slot:button-content>
                        <?= Icon::widget()->name('settings'); ?>
                    </template>
                    <?= ActivityLogLink::widget()
                        ->tag('b-dropdown-item')
                        ->label('Activity log')
                        ->entity($user); ?>
                </b-dropdown>
                <div class="btn-toolbar float-right">
                    <a class="btn btn-sm btn-outline-secondary mx-sm-1 mb-2" href="<?= $urlGenerator->generate('/user/default/index'); ?>">
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <?= DetailView::widget()
            ->data($user)
            ->options([
                'class' => 'table detail-view',
            ])
            ->emptyText('(not set)')
            ->emptyTextOptions([
                'class' => 'text-muted',
            ])
            ->attributes([
                [
                    'label' => 'Email',
                    'value' => function (User $data, $index) {
                        return $data->getEmail();
                    },
                ],
                [
                    'label' => 'Username',
                    'value' => function (User $data, $index) {
                        return $data->getUsername();
                    },
                ],
                [
                    'label' => 'Status',
                    'value' => function (User $data, $index) {
                        return $data->getStatus();
                    },
                ],
            ]);
        ?>
    </div>
</div>
