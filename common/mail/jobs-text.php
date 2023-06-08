<?php

/* @var $this yii\web\View */
/* @var $resume frontend\form\JobsSend */

?>
Отлкли на вакансию

<?php foreach ($resume->attributes as $key => $val): ?>
    <? if ($key != 'verifyCode' && $key != 'resume'): ?>
        <?= $resume->getAttributeLabel($key) ?>: <?= $val ?>

    <? endif ?>
<?php endforeach; ?>
