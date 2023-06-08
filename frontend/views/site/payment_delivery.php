<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $page backend\models\Pages
 */
$context = $this->context;
?>
<div class="TextContent padSpace">
    <h1 class="title"><?=$page->name?></h1>
    <div class="textInterface">
       <?=$page->body?>
    </div>
</div>
<div class="modulePayment padSpace">
    <div class="leftBlock">Мы принимаем к оплате</div>
    <ul>
        <li>
            <div class="image"><img src="<?=$context->AppAsset->baseUrl?>/images/icons/icon_visa.png" style="margin-top:14px;" alt="VISA"/></div>
            <span>Карты Visa</span>
        </li>
        <li>
            <div class="image"><img src="<?=$context->AppAsset->baseUrl?>/images/icons/icon_master.png" alt="Mastercard"/></div>
            <span>Карты MasterCard</span>
        </li>
        <li>
            <div class="image"><img src="<?=$context->AppAsset->baseUrl?>/images/icons/icon_tenge.png" alt="Tenge"/></div>
            <span>Наличные</span>
        </li>
    </ul>
</div>
