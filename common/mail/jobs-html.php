<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $resume frontend\form\JobsSend */
?>
<div>
    <p>Отлкли на вакансию</p>
    <?php foreach ($resume->attributes as $key => $val): ?>
        <? if ($key != 'verifyCode' && $key != 'resume'): ?>
            <p><?= $resume->getAttributeLabel($key) ?>: <?= $val ?></p>
        <? endif ?>
    <?php endforeach; ?>
</div>
