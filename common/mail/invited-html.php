<?php
/**
 * @var $this yii\web\View
 * @var $item frontend\form\InvitedSend
 * @var $user common\models\User
 */
use yii\helpers\Html;

?>
<div>
    <p>Здравствуйте! Пользователь <?= $user->username ?> приглашает вас на наш сайт <?= Html::a('ссылка', ['site/index', 'code' => $user->code], ['target' => '_blank']) ?></p>
</div>
