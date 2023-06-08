<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 */
use common\models\City;
use shadow\widgets\SAuthChoice;
use yii\helpers\Json;
use yii\helpers\Url;

$context = $this->context;
$assets = frontend\assets\ActiveFormAsset::register($this);
$this->registerCss(<<<CSS
#loader {
	display:none;
	width:100%;
	height:100%;
	background: #E8E4E4;
	opacity:.9;
	top: 0;
	-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(opacity=90)";
	position:fixed;
	z-index:9999992;
}
#loader img {
    left: 50%;
    top: 50%;
    position: fixed;
    margin-top: -64px;
    margin-left: -64px;
}
.error_all{
	color: #e7a19a;
}
.tooltipster-morkovka {
	padding: 7px 6px 9px!important;
	background: #fff;
	border: 1px solid #e39891;
	border-radius: 2px;
	font-size: 8px!important;
}
.tooltipster-morkovka .tooltipster-content {
	font: 11px/1.2 "Roboto";
	padding: 0 0 0 12px !important;
	color: #f4574b !important;
	position: relative;
}
.tooltipster-morkovka .tooltipster-content:before {
	content: "";
	width: 12px;
	height: 11px;
	position: absolute;
	left: 0;
	background: url(../img/error-icon.png) no-repeat;
}
CSS
);
if (\Yii::$app->session->get('success_order')) {
    $this->registerJs(<<<JS
$.colorbox({href:"#popup_1",open:true,inline: true, width: "350px"});
JS
        , $this::POS_LOAD);
    \Yii::$app->session->remove('success_order');
}
?>
<div id="overlay"></div>
<!--For developer-->
<div id="loader">
    <img src="<?= $assets->baseUrl ?>/images/loading.gif" alt="">
</div>

<div class="overlayWinmod">

<div id="send_form_success">
    <div class="pop-up1">
        <div class="close" onclick="popup({block_id: '#send_form_success', action: 'close'});">+</div>
        <span class="text"></span>
    </div>
</div>
<!--//For developer-->

<?= $this->render('fast_order') ?>

<div id="popupGoodsProcessing" class="popup window">
    <div class="popupClose" onclick="popup({block_id: '#popupGoodsProcessing', action: 'close'});"></div>
    <div class="popupTitle">Обработка товара</div>
    <form class="formGoodsProcessing" id="form_type_handling">
        <div class="string twoCol" id="popup_type_handling">
            <? if (false): ?>
                <div class="col">
                    <input type="checkbox" id="qwer1" />
                    <label for="qwer1">
                        <div class="image">
                            <img src="/uploads/Goods/chist.png" alt="" />
                        </div>
                        Почистить
                    </label>
                </div>
                <div class="col">
                    <input type="checkbox" id="qwer2" />
                    <label for="qwer2">
                        <div class="image">
                            <img src="/uploads/Goods/narez.png" alt="" />
                        </div>
                        Порезать
                    </label>
                </div>
            <? endif ?>
        </div>
        <div class="string">
            <div class="popupText">
                Вы всегда сможете изменить обработку в корзине
            </div>
        </div>
        <div class="string twoBtn">
            <button class="btn_Form blue" type="button" id="send_type_handling">Сохранить</button>
            <div class="btn_Form grey" onclick="popup({block_id: '#popupGoodsProcessing', action: 'close'});">Спасибо, не нужно</div>
        </div>
    </form>
</div>
<div id="popupOrderByClick" class="popup window">
    <div class="popupClose" onclick="popup({block_id: '#popupOrderByClick', action: 'close'});"></div>
    <div class="orderByClick_content">
        <div class="buttonsLine">
            <a href="<?= Url::to(['site/basket']) ?>" class="btn_Form blue">Оформить заказ</a>
            <span onclick="popup({block_id: '#popupOrderByClick', action: 'close'});">Продолжить покупки</span>
        </div>
        <div class="popupText" id="text_delivery_popup">
            <p>Ваш заказ менее <b id="min_sum_delivery_popup">8 000 т.</b>, добавьте товаров на сумму <b id="price_delivery_popup">2 000 т.</b>, чтобы получить бесплатную доставку </p>
        </div>
    </div>
</div>


</div>