<?php
/**
 * @var $this yii\web\View
 * @var $items common\models\ReviewsItem[]
 * @var $context backend\controllers\SiteController
 */
use yii\helpers\Html;
use yii\helpers\Url;

$context = $this->context;
$url = $context->action->id;
?>
<?= $this->render('//blocks/breadcrumb') ?>
<section id="content">
    <div class="panel">
        <div class="panel-heading">
            <a href="<?= Url::to([$url . '/control']) ?>" class="btn-primary btn">
                <i class="fa fa-plus"></i> <span class="hidden-xs hidden-sm">Добавить</span></a>
        </div>
        <div class="panel-body">
            <table class="table-primary table table-striped table-hover">
                <colgroup>
                    <col width="25px">
                    <col>
                    <col>
                    <col>
                    <col width="25px">
                </colgroup>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Имя</th>
                    <th class="text-center">Статус</th>
                    <th class="text-center">Дата создания</th>
                    <th class="text-right">Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr id="layout_normal">
                        <th><?=$item->id?></th>
                        <th>
                            <a href="<?= Url::to([$url.'/control','id'=>$item->id]) ?>">
                                <?=$item->name?>
                            </a>
                        </th>
                        <th class="text-center"><?=($item->isVisible)?'Видим':'Скрыт'?></th>
                        <th class="text-center"><?= date('d.m.y', $item->created_at) ?></th>
                        <td class="actions text-right">
                            <a href="<?= Url::to([$url . '/control', 'id' => $item->id]) ?>" class="btn-success btn-xs btn">
                                <i class="fa fa-pencil fa-inverse"></i>
                            </a>
                            <a href="<?= Url::to([$url . '/deleted', 'id' => $item->id]) ?>" class="btn-danger btn-xs btn-confirm btn">
                                <i class="fa fa-times fa-inverse"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>