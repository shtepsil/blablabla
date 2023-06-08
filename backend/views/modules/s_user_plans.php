<?php
/**
 * @var $this yii\web\View
 * @var $items backend\models\SUserPlan[]
 * @var $context backend\controllers\SiteController
 */
use backend\models\SUser;
use yii\helpers\Inflector;
use yii\helpers\Url;

$context = $this->context;
$url = Inflector::camel2id($context->id);
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
                    <col>
                    <col>
                    <col>
                    <col>
                    <col width="25px">
                </colgroup>
                <thead>
                <tr>
                    <th>Менеджер</th>
                    <th class="text-center">Сумма</th>
                    <th class="text-center">Дата начала</th>
                    <th class="text-center">Дата окончания</th>
                    <th class="text-right">Действия</th>
                </tr>
                </thead>
                <tbody>
                <?
                /**
                 * @var $all_users SUser[]
                 */
                $all_users = SUser::find()->indexBy('id')->all();
                ?>
                <?php foreach ($items as $item): ?>
                    <tr id="layout_normal">
                        <th>                                <a href="<?= Url::to([$url.'/control','id'=>$item->id]) ?>">

                            <? if($item->user_id): ?>
                                    <?=$all_users[$item->user_id]->username?>
                            <? else: ?>
                                Для всех
                            <? endif; ?>
                            </a>

                        </th>
                        <th class="text-center"><?=$item->sum?></th>
                        <th class="text-center"><?= date('d.m.y', $item->date_start) ?></th>
                        <th class="text-center"><?= date('d.m.y', $item->date_end) ?></th>
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
