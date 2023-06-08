<?php
use common\components\Debugger as d;
use backend\models\FooterMenu;
use common\models\Category;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;

/**
 * @var $context \frontend\controllers\SiteController
 * @var $this \yii\web\View
 * @var $content string
 */
$context = $this->context;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>" />
	<?php
    $meta_og = '
	<meta property="og:title" content="'.Html::encode($this->title).'"/>
	<meta property="og:image" content="https://site.ru/assets/f5d2a9d/images/bg_site.jpg"/>
	<meta property="og:type" content="profile"/>
	<meta property="og:url" content= "https://site.ru" />';
//    echo $meta_og;
    ?>
    <link rel="shortcut icon" href="<?= $context->AppAsset->baseUrl ?>/images/favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        #header_logo_visa{
            display: block;
            float: right;
            padding-top: 17px;
            padding-right: 50px;
        }
        @media (max-width: 999px){
            #header_logo_visa{
                display: none;
            }
        }
	</style>
	<meta name="yandex-verification" content="ca8d7b4f31888d23" />
	<meta name="google-site-verification" content="gUAoIUmyL6YXM0Q6JY41_jmaw2CXwRP5NHNLoBy0iXw" />
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-M4QRD2T');</script>
<!-- End Google Tag Manager -->	
</head>
<body>
<?if(d::isLocal()):?>
    <style>
        .view-user-info{
            position: fixed;
            padding: 10px;
            color: white;
            background-color: rgba(24,18,69,1);
            font-family: sans-serif;
            top: 0px;
            z-index: 10;
            font-size: 14px;
            line-height: 20px;
        }
    </style>
    <?
    if(!Yii::$app->user->isGuest){
        $user_info = Yii::$app->user->identity;
    ?>
    <div class="view-user-info">
        <?=$user_info->email?><br>
        <?=$user_info->username?><br>
        ID: <?=$user_info->id?><br>
        <?if($user_info->isWholesale > 0):?>
            Оптовик: <?=$user_info->isWholesale?>
        <?else:?>
            Обычный пользователь
        <?endif?>
    </div>
<? } else { ?>
    <div class="view-user-info">Вы гость</div>
<? } ?>
<? endif?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M4QRD2T"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
	
<?php $this->beginBody() ?>
<?= $this->render('//popups/all') ?>
<? if ($context->settings->get('text_header_enable')&&!Yii::$app->request->cookies->getValue('text_header_enable')): ?>
    <div id="topInform">
        <div class="innerWrapper">
            <span><?= $context->settings->get('text_header') ?></span>
            <div class="close close_block" data-id="text_header_enable" data-close="#topInform"></div>
        </div>
    </div>
<? endif ?>
<div id="global">
    <?php if ($context->id == 'site' && $context->action->id == 'index'): ?>
        <div class="wrapperAdapt">
            <?= $this->render('//blocks/header_menu') ?>
        </div>
    <?php else: ?>
        <?= $this->render('//blocks/header_menu') ?>
    <?php endif; ?>
    <?= $content ?>
</div>
	
<footer class="footer" itemscope itemtype="http://schema.org/Organization">
    <div class="copyright">
        <p>© <? echo date("Y");?> <span itemprop="name">Site</span></p>
        <p itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
			<span itemprop="addressLocality">Город</span>,
			<span itemprop="streetAddress">Адрес магазина.</span>
		</p>
        <p itemprop="telephone">+7 708 500 00 90</p>
        <a itemprop="email" href="mailto:info@site.ru">info@site.ru</a>
		<script type="application/ld+json">
			{
			  "@context": "https://schema.org",
			  "@type": "Organization",
			  "url": "http://site.ru",
			  "logo": "https://site.ru/assets/f5d2a9d/images/logotype.png"
			}
    	</script>
    </div>
    <div class="fMenu">
        <?
        /**
         * @var $cat Category
         * @var $cat_menus FooterMenu[]
         * @var $menu FooterMenu
         */
        $cat_menus = FooterMenu::find()
            ->orderBy(['footer_menu.sort' => SORT_ASC])
            ->with(['cat'])
            ->where(['footer_menu.isVisible' => 1, 'footer_menu.parent_id' => null])
            ->all();
        $a_cat_menus = array_chunk($cat_menus, 4);
        ?>
        <? foreach ($a_cat_menus as $a_cat_menu): ?>
            <ul>
                <?php
                foreach ($a_cat_menu as $menu) {
                    echo Html::tag('li', Html::a($menu->name, $menu->createUrl()));
                }
                ?>
            </ul>
        <? endforeach; ?>
			
			<div>
				<a href=" https://instagram.com/siteru">
				<img src="<?= $context->AppAsset->baseUrl ?>/images/icons/insta.png" style="width:90px;margin-left:100px;margin-top:0" alt="instagram" />
				</a>
			</div> 
    </div> 
    <div class="fFacebookWrapper">	
        <a href="<?=$context->settings->get('url_facebook','#')?>" class="fFacebook">
            <img src="<?= $context->AppAsset->baseUrl ?>/images/icons/icon_f.png" alt="facebook" />
            <span>Общайтесь с нами <br />в  <span>FACEBOOK</span></span>
        </a>
    </div>
</footer>
	
<?
$this->registerJs(<<<JS
//JS

$(document).ready(function(){
    
});
                  
function onstorage(options) {
    location.reload();
}
JS
    , $this::POS_END)
?>
<?
$url_close_block = Json::encode(Url::to(['api/close']));
$this->registerJs(<<<JS
$('.close_block').click(function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    $($(this).data('close')).remove();
    $.ajax({
        url: {$url_close_block},
        type: 'GET',
        data: {id: id}
    })
})
JS
)
?>
<?php $this->endBody() ?>
<?= $context->settings->get('service_scripts') ?>	
</body>
</html>
<?php $this->endPage() ?>
