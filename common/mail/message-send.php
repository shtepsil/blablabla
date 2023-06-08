<?php
/**
 * @var $this yii\web\View
 * @var $item frontend\form\MessageSend
 */
?>
<div>
    <p>Сообщение с сайта</p>
    <?php foreach ($item->attributes as $key => $val): ?>
        <? if ($key != 'id'&&$key != 'created_at'&&$key != 'updated_at'&&$key != 'verifyCode'): ?>
            <p><?= $item->getAttributeLabel($key) ?>: <?= $val ?></p>
        <? endif ?>
    <?php endforeach; ?>
</div>
