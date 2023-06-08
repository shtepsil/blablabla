<?php

/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\UserController
 */
use common\models\HistoryBonus;
use yii\helpers\Url;

$context = $this->context;
$user = $context->user;
/**
 * @var $history_bonus HistoryBonus[]
 */
$history_bonus = HistoryBonus::find()->andWhere(['user_id' => $user->id])->limit(20)->orderBy(['created_at' => SORT_DESC])->all();
$percent_bonus = $context->function_system->percent();
$add_bonus=floor((100 * ($percent_bonus)) / 100)
?>
<div class="breadcrumbsWrapper padSpace">
    <?= $this->render('//blocks/breadcrumbs') ?>
</div>
<div class="Cabinet padSpace">

    <div class="gTitle">Мои бонусы</div>

    <div class="cDescription">
        <p>Ваш текущий баланс <b><?= number_format($user->bonus, 0, '', ' ')?> бонусов (<?= number_format($user->bonus, 0, '', ' ')?> тг.)</b></p>

        <p>
            Текущий обменный курс: <br/>
            <b>1 бонус = 1 тг. скидки</b> <br/>
            За каждые 100 потраченных тенге вы получите <?=$add_bonus?> бонусов
        </p>
    </div>

    <table class="adpTable orders">
        <thead>
        <tr>
            <td class="zNum">Причина начисления</td>
            <td class="zDate">Дата</td>
            <td class="zSumm">Сумма</td>
        </tr>
        </thead>
        <tbody>
        <? foreach($history_bonus as $history): ?>
            <tr class="success">
                <td class="zNum" data-title="Причина начисления"><?=$history->name?></td>
                <td class="zDate" data-title="Дата"><?= Yii::$app->formatter->asDate($history->created_at, 'd MMMM Y '); ?></td>
                <td class="zSumm" data-title="Сумма"><b><?= number_format($history->sum, 0, '', ' ')?></b></td>
            </tr>
        <? endforeach; ?>
        </tbody>
    </table>

</div>