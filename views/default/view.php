<?php declare(strict_types=1);

use Mailery\User\Entity\User;
use Yiisoft\Yii\DataView\DetailView;
use Yiisoft\Yii\Widgets\ContentDecorator;
use Yiisoft\Html\Html;
use Mailery\Web\Vue\Directive;

/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\User\Entity\User $user */
/** @var Yiisoft\Rbac\Manager $manager */
/** @var Yiisoft\Router\UrlGeneratorInterface $url */
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
            ->model($user)
            ->options([
                'class' => 'table detail-view',
            ])
            ->emptyValue('<span class="text-muted">(not set)</span>')
            ->attributes([
                [
                    'label' => 'Email',
                    'value' => function (User $data) {
                        return Directive::pre($data->getEmail());
                    },
                ],
                [
                    'label' => 'Username',
                    'value' => function (User $data) {
                        return Directive::pre($data->getUsername());
                    },
                ],
                [
                    'label' => 'Roles',
                    'value' => function (User $data) use($manager, $url) {
                        $links = [];
                        foreach ($manager->getRolesByUserId($data->getId()) as $role) {
                            $links[] = Html::a(
                                $role->getName(),
                                $url->generate('/rbac/role/view', ['name' => $role->getName()])
                            );
                        }
                        return implode('<br />', $links);
                    },
                ],
                [
                    'label' => 'Status',
                    'value' => function (User $data) {
                        return '<span class="badge ' . $data->getStatus()->getCssClass() . '">' . Directive::pre($data->getStatus()->getLabel()) . '</span>';
                    },
                ],
                [
                    'label' => 'Country',
                    'value' => function (User $data) {
                        return Directive::pre($data->getCountry()->getLabel());
                    },
                ],
                [
                    'label' => 'Timezone',
                    'value' => function (User $data) {
                        return Directive::pre($data->getTimezone()->getLabel());
                    },
                ],
            ]);
        ?>
    </div>
</div>

<?= ContentDecorator::end() ?>
