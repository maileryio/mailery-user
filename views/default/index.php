<?php declare(strict_types=1);

use Mailery\Icon\Icon;
use Mailery\Activity\Log\Widget\ActivityLogLink;
use Mailery\User\Module;
use Mailery\User\Entity\User;
use Mailery\Widget\Dataview\Columns\ActionColumn;
use Mailery\Widget\Dataview\Columns\DataColumn;
use Mailery\Widget\Dataview\GridView;
use Mailery\Widget\Dataview\GridView\LinkPager;
use Mailery\Widget\Link\Link;
use Mailery\Widget\Search\Widget\SearchWidget;
use Yiisoft\Html\Html;

/** @var Mailery\Web\View\WebView $this */
/** @var Mailery\Widget\Search\Form\SearchForm $searchForm */
/** @var Yiisoft\Aliases\Aliases $aliases */
/** @var Yiisoft\Router\UrlGeneratorInterface $urlGenerator */
/** @var Yiisoft\Data\Reader\DataReaderInterface $dataReader*/
/** @var Yiisoft\Data\Paginator\PaginatorInterface $paginator */
$this->setTitle('All users');

?><div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3">
            <h1 class="h2">All users</h1>
            <div class="btn-toolbar float-right">
                <?= SearchWidget::widget()->form($searchForm); ?>
                <b-dropdown right size="sm" variant="secondary" class="mb-2">
                    <template v-slot:button-content>
                        <?= Icon::widget()->name('settings'); ?>
                    </template>
                    <?= ActivityLogLink::widget()
                        ->tag('b-dropdown-item')
                        ->label('Activity log')
                        ->module(Module::NAME); ?>
                </b-dropdown>
                <a class="btn btn-sm btn-primary mx-sm-1 mb-2" href="<?= $urlGenerator->generate('/user/default/create'); ?>">
                    <?= Icon::widget()->name('plus')->options(['class' => 'mr-1']); ?>
                    Add new user
                </a>
            </div>
        </div>
    </div>
</div>
<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <?= GridView::widget()
            ->paginator($paginator)
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
            ->columns([
                (new DataColumn())
                    ->header('Username')
                    ->content(function (User $data, int $index) {
                        return $data->getUsername();
                    }),
                (new DataColumn())
                    ->header('Email')
                    ->content(function (User $data, int $index) use ($urlGenerator) {
                        return Html::a(
                            $data->getEmail(),
                            $urlGenerator->generate('/user/default/view', ['id' => $data->getId()])
                        );
                    }),
                (new DataColumn())
                    ->header('Status')
                    ->content(function (User $data, int $index) {
                        return $data->getStatus();
                    }),
                (new ActionColumn())
                    ->contentOptions([
                        'style' => 'width: 80px;',
                    ])
                    ->header('Edit')
                    ->view('')
                    ->update(function (User $data, int $index) use ($urlGenerator) {
                        return Html::a(
                            (string) Icon::widget()->name('pencil'),
                            $urlGenerator->generate('/user/default/edit', ['id' => $data->getId()]),
                            [
                                'class' => 'text-decoration-none mr-3',
                            ]
                        );
                    })
                    ->delete(''),
                (new ActionColumn())
                    ->contentOptions([
                        'style' => 'width: 80px;',
                    ])
                    ->header('Delete')
                    ->view('')
                    ->update('')
                    ->delete(function (User $data, int $index) use ($urlGenerator) {
                        return Link::widget()
                            ->label((string) Icon::widget()->name('delete')->options(['class' => 'mr-1']))
                            ->method('delete')
                            ->href($urlGenerator->generate('/user/default/delete', ['id' => $data->getId()]))
                            ->confirm('Are you sure?')
                            ->options([
                                'class' => 'text-decoration-none text-danger',
                            ]);
                    }),
            ]);
        ?>
    </div>
</div><?php
if ($paginator->getTotalCount() > 0) {
            ?><div class="mb-4"></div>
    <div class="row">
        <div class="col-6">
            <?= GridView\OffsetSummary::widget()
                ->paginator($paginator); ?>
        </div>
        <div class="col-6">
            <?= LinkPager::widget()
                ->paginator($paginator)
                ->options([
                    'class' => 'float-right',
                ])
                ->prevPageLabel('Previous')
                ->nextPageLabel('Next')
                ->urlGenerator(function (int $page) use ($urlGenerator) {
                    $url = $urlGenerator->generate('/user/default/index');
                    if ($page > 1) {
                        $url = $url . '?page=' . $page;
                    }

                    return $url;
                }); ?>
        </div>
    </div><?php
        }
?>
