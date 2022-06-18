<?php declare(strict_types=1);

use Mailery\Icon\Icon;
use Mailery\Activity\Log\Widget\ActivityLogLink;
use Mailery\User\Entity\User;
use Mailery\Widget\Link\Link;
use Mailery\Widget\Search\Widget\SearchWidget;
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\GridView;

/** @var Yiisoft\Yii\WebView $this */
/** @var Mailery\Widget\Search\Form\SearchForm $searchForm */
/** @var Yiisoft\Aliases\Aliases $aliases */
/** @var Yiisoft\Router\UrlGeneratorInterface $url */
/** @var Yiisoft\Data\Paginator\PaginatorInterface $paginator */
$this->setTitle('All users');

?><div class="row">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md">
                        <h4 class="mb-0">All users</h4>
                    </div>
                    <div class="col-auto">
                        <div class="btn-toolbar float-right">
                            <?= SearchWidget::widget()->form($searchForm); ?>
                            <b-dropdown right size="sm" variant="secondary" class="mb-2">
                                <template v-slot:button-content>
                                    <?= Icon::widget()->name('settings'); ?>
                                </template>
                                <?= ActivityLogLink::widget()
                                    ->tag('b-dropdown-item')
                                    ->label('Activity log')
                                    ->group('user'); ?>
                            </b-dropdown>
                            <a class="btn btn-sm btn-primary mx-sm-1 mb-2" href="<?= $url->generate('/user/default/create'); ?>">
                                <?= Icon::widget()->name('plus')->options(['class' => 'mr-1']); ?>
                                Add new user
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <?= GridView::widget()
                    ->layout("{items}\n<div class=\"mb-4\"></div>\n{summary}\n<div class=\"float-right\">{pager}</div>")
                    ->options([
                        'class' => 'table-responsive',
                    ])
                    ->tableOptions([
                        'class' => 'table table-hover',
                    ])
                    ->emptyText('No data')
                    ->emptyTextOptions([
                        'class' => 'text-center text-muted mt-4 mb-4',
                    ])
                    ->paginator($paginator)
                    ->currentPage($paginator->getCurrentPage())
                    ->columns([
                        [
                            'label()' => ['Username'],
                            'value()' => [fn (User $model) => $model->getUsername()],
                        ],
                        [
                            'label()' => ['Email'],
                            'value()' => [fn (User $model) => Html::a($model->getEmail(), $url->generate($model->getViewRouteName(), $model->getViewRouteParams()))],
                        ],
                        [
                            'label()' => ['Country'],
                            'value()' => [fn (User $model) => $model->getCountry()->getLabel()],
                        ],
                        [
                            'label()' => ['Timezone'],
                            'value()' => [fn (User $model) => $model->getTimezone()->getLabel()],
                        ],
                        [
                            'label()' => ['Status'],
                            'value()' => [fn (User $model) => '<span class="badge ' . $model->getStatus()->getCssClass() . '">' . $model->getStatus()->getLabel() . '</span>'],
                        ],
                        [
                            'label()' => ['Edit'],
                            'value()' => [static function (User $model) use ($url) {
                                return Html::a(
                                    Icon::widget()->name('pencil')->render(),
                                    $url->generate($model->getEditRouteName(), $model->getEditRouteParams()),
                                    [
                                        'class' => 'text-decoration-none mr-3',
                                    ]
                                )
                                ->encode(false);
                            }],
                        ],
                        [
                            'label()' => ['Delete'],
                            'value()' => [static function (User $model) use ($csrf, $url) {
                                return Link::widget()
                                    ->csrf($csrf)
                                    ->label(Icon::widget()->name('delete')->options(['class' => 'mr-1'])->render())
                                    ->method('delete')
                                    ->href($url->generate($model->getDeleteRouteName(), $model->getDeleteRouteParams()))
                                    ->confirm('Are you sure?')
                                    ->options([
                                        'class' => 'text-decoration-none text-danger',
                                    ])
                                    ->encode(false);
                            }],
                        ],
                    ]);
                ?>
            </div>
        </div>
    </div>
</div>
