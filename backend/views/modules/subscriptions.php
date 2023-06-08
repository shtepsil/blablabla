<?php
/**
 * @var $this yii\web\View
 * @var $items common\models\Subscriptions[]
 * @var $context backend\controllers\UsersController
 */
use yii\helpers\Url;

$context = $this->context;
?>
<?= $this->render('//blocks/breadcrumb') ?>
<section id="content">
    <div class="panel">
        <div class="panel-heading">
            <a href="<?= Url::to(['subscriptions/export']) ?>" class="btn-primary btn" target="_blank">
                <i class="fa fa-upload"></i> <span class="hidden-xs hidden-sm"> Экспорт</span></a>
        </div>
        <table class="table-primary table table-striped table-hover">
            <colgroup>
                <col width="150px">
            </colgroup>
            <thead>
            <tr>
                <th>Почта</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr id="layout_normal">
                    <th >
                        <?=$item['email']?>
                    </th>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
