<?php declare(strict_types=1);

use Mailery\User\Entity\User;
use Mailery\Widget\Dataview\DetailView;
use Yiisoft\Yii\Widgets\ContentDecorator;

/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\User\Entity\User $user */
/** @var Yiisoft\Yii\View\Csrf $csrf */

$this->setTitle($user->getUsername());

?>

<?= ContentDecorator::widget()
    ->viewFile('@vendor/maileryio/mailery-user/views/default/_layout.php')
    ->parameters(compact('user', 'csrf'))
    ->begin(); ?>

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

<?= ContentDecorator::end() ?>
