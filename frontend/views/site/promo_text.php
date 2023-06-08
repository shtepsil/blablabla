<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $status string
 *
 */
$context = $this->context;
?>
<div class="TextContent padSpace">
    <h1 class="title">Активация кода</h1>
    <div class="textInterface">
        <? if($status == 'empty'): ?>
            <h2>Данный код уже недействителен</h2>
        <? else: ?>
            <h2>Вы уже активировали данный код</h2>
        <? endif; ?>
    </div>
</div>