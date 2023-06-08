<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $item \common\models\Items
 *
 */
$context = $this->context;
?>
<div class="TextContent padSpace">
    <h1 class="title"></h1>
    <div class="textInterface">
        <h2>Вы выиграли! Обратитесь к менеджеру для получения приза.</h2>
        <div style="text-align: left">
            <img src="<?= $item->img() ?>" alt="" style="max-width: 200px;">
            <div><?= $item->name ?></div>
        </div>
    </div>
</div>