<?php
/**
 * @var $this yii\web\View
 * @var $items common\models\Brands[]
 */
use yii\helpers\Url;

?>
<section id="content">
    <div class="panel">
        <div class="panel-heading">
            <a href="<?= Url::to(['brands/control']) ?>" class="btn-primary btn">
                <i class="fa fa-plus"></i> <span class="hidden-xs hidden-sm">Добавить</span></a>
        </div>
        <table class="table-primary table table-striped table-hover">
            <colgroup>
                <col>
                <col width="150px">
                <col width="100px">
                <col width="100px">
                <col width="100px">
            </colgroup>
            <thead>
            <tr>
                <th>Название</th>
                <th class="text-right">Действия</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr id="layout_normal">
                    <th class="name">
                        <a href="<?= Url::to(['brands/control', 'id' => $item->id]) ?>"><?= $item->name ?></a>
                    </th>
                    <td class="actions text-right">
                        <a href="<?= Url::to(['brands/deleted', 'id' => $item->id]) ?>" class="btn-danger btn-xs btn-confirm btn">
                            <i class="fa fa-times fa-inverse"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>