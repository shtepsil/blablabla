<?php
/**
 * @var $this yii\web\View
 * @var $items common\models\Options[]
 */
use yii\helpers\Inflector;
use yii\helpers\Url;

$url = 'type_recipes';
?>
<?= $this->render('//blocks/breadcrumb') ?>

<section id="content">
    <div class="panel">
        <div class="panel-heading">
            <a href="<?= Url::to([$url.'/control']) ?>" class="btn-primary btn">
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
                        <a href="<?= Url::to([$url.'/control', 'id' => $item->id]) ?>"><?= $item->name ?></a>
                    </th>
                    <td class="actions text-right">
                        <a href="<?= Url::to([$url.'/deleted', 'id' => $item->id]) ?>" class="btn-danger btn-xs btn-confirm btn">
                            <i class="fa fa-times fa-inverse"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>