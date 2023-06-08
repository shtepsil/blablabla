<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */
/**
 * @var $context \frontend\controllers\SiteController
 */
$context = $this->context;
$this->title = $name;
?>

<div class="errorPage404">
    <div class="image404">
        <img src="<?=$context->AppAsset->baseUrl?>/images/404.png" alt="404">
    </div>
    <div class="erInformMessg">Страница была удалена или никогда не существовала</div>
    <a href="<?= Url::to(['site/index']) ?>" class="gotohome">Перейти на главную</a>
</div>