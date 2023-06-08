<?php
/**
 * @var $this yii\web\View
 * @var $items common\models\SpecActionPhone[]
 * @var $context backend\controllers\ActionsController
 */


$context = $this->context;
$url = $context->id;
?>
<?= $this->render('//blocks/breadcrumb') ?>
<section id="content">
    <div class="panel">
        <div class="panel-body">
            <table class="table-primary table table-striped table-hover">
                <colgroup>
                    <col width="25px">
                    <col>
                    <col>
                    <col>
                    <col>
                </colgroup>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Имя</th>
                    <th>Номер</th>
                    <th>SMS код</th>
                    <th>Активирован</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr id="layout_normal">
                        <th><?= $item->id ?></th>
                        <th><?= $item->name ?></th>
                        <th><?= $item->phone ?></th>
                        <th><?= $item->code ?></th>
                        <th><?= ($item->status?'Да':'Нет') ?></th>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>