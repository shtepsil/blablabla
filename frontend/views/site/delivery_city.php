<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $item \common\models\City
 *
 */
$context = $this->context;
?>
<div class="TextContent padSpace">
	<h1 class="title"><?= Yii::t('main', 'Доставка в городе {name}', ['name' => $item->name]) ?></h1>
	<div class="textInterface">
        <?= $item->info_delivery ?>
	</div>
</div>