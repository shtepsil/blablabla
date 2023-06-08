<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $items_hit \common\models\Items[]
 * @var $items_sale \common\models\Items[]
 * @var $recipe_day \common\models\Recipes
 * @var $recipes \common\models\Recipes[]
 * @var $banners \common\models\Banners[]
 * @var $actions \common\models\Actions[]
 */

use common\components\Debugger as d;
use common\models\Reviews;
use frontend\form\Subscription;
use frontend\widgets\ActiveField;
use frontend\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use common\helpers\DateHelper;

$context = $this->context;
//d::pri($_SERVER['REMOTE_ADDR']);
if($_SERVER['REMOTE_ADDR'] == '2.62.164.130'){
	
}
?>
<? if ($banners): ?>
    <?php
    $content_li = '';
    $content_img = '';
    foreach ($banners as $key => $banner) {
        $class_li = '';
        $class_img = 'item';
        if ($key == 0) {
            $class_img .= ' active';
            $class_li .= 'active';
        }
        $content_li .= Html::tag('li', '',
            [
                'data' => [
                    'target' => '#carousel-example-generic',
                    'slide-to' => $key
                ],
                'class' => $class_li
            ]);
        if (\Yii::$app->params['devicedetect']['isMobile']&&$banner->img_mobile) {
            $content_img .= Html::a('', $banner->url,
                [
                    'class' => $class_img,
                    'style' => "background-image: url({$banner->img_mobile})"
                ]);
        } else {
            $content_img .= Html::a('', $banner->url,
                [
                    'class' => $class_img,
                    'style' => "background-image: url({$banner->img})"
                ]);
        }
    }
    ?>
<!--    <div id="headSlider" class="sliderPosition">-->
<!--        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel" data-interval="10000">-->
          <!-- Indicators -->
<!--            <ol class="carousel-indicators">-->
<!--                --><?//= $content_li ?>
<!--            </ol>-->
           <!-- Wrapper for slides -->
<!--            <div class="carousel-inner" role="listbox">-->
<!--                --><?//= $content_img ?>
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<?php if (count($banners) > 1):?>
    <div id="headerSlider" class="sliderPosition">
        <div class="carousel-inner owl-carousel-0">
            <?=$content_img; ?>
        </div>
    </div>
	<?php endif?>
<? endif ?>
<ul class="informLine padSpace">
    <li class="one">
        <div class="table">
            <img src="<?= $context->AppAsset->baseUrl ?>/images/informLine/1.png" alt="" />
            <span>Бесплатная доставка</span></div>
    </li>
    <li class="two">
        <div class="table">
            <img src="<?= $context->AppAsset->baseUrl ?>/images/informLine/2.png" alt="" />
            <span>Возврат товара, <br /> если не понравилось качество</span></div>
    </li>
    <li class="three">
        <div class="table">
            <img src="<?= $context->AppAsset->baseUrl ?>/images/informLine/3.png" alt="" />
            <span>Порезанный и почищенный продукт</span></div>
    </li>
    <li class="four">
        <div class="table">
            <img src="<?= $context->AppAsset->baseUrl ?>/images/informLine/4.png" alt="" />
            <span>Оплата в момент доставки</span></div>
    </li>
    <li class="five">
        <div class="table">
            <img src="<?= $context->AppAsset->baseUrl ?>/images/informLine/5.png" alt="" />
            <span>Бонус за каждую покупку</span></div>
    </li>
</ul>
<section class="slPosition_line">
    <div class="gTitle">Самые популярные продукты</div>
    <div class="homeLine">
        <div class="wrapperSl">
            <div class="owl-carousel-1">
                <?php foreach ($items_hit as $item_hit): ?>
                    <?php
                    $class = '';
                    if (!$item_hit->count($context->city)) {
                        $class .= ' isNone';
                    }
                    ?>
                        <div class="goodsBlock<?=$class?>">
                            <a class="image" href="<?= $item_hit->url() ?>" style="background-image: url(<?= $item_hit->img(true,'item_320') ?>);">
                                <? if ($item_hit->old_price || $item_hit->discount || $item_hit->isNew): ?>
                                    <span class="stickerPosition">
                                        <? if ($item_hit->old_price || $item_hit->discount): ?>
                                            <span class="action">Акция</span>
                                        <? endif ?>
                                        <? if ($item_hit->isNew): ?>
                                            <span class="new">Новинка</span>
                                        <? endif ?>
                                        <? if ($item_hit->discount): ?>
                                            <span class="discount">-<?= $item_hit->discount ?>%</span>
                                        <? endif ?>
                                    </span>
                                <? endif ?>
                            </a>
                            <span class="wrapperPad">
                                <a class="title" href="<?= $item_hit->url() ?>"><span><?= $item_hit->name ?></span></a>
                                <span class="pricePosition">
                                    <span class="text">Цена за 1 <?= ($item_hit->measure_price==1) ? 'шт' : 'кг' ?>.</span>
                                    <span class="price">
                                        <span class="new"><?= number_format($item_hit->real_price(true), 0, '', ' ')
                                            ?> Т</span>
                                        <? if ($item_hit->old_price): ?>
                                            <span class="old"><?= number_format($item_hit->old_price, 0, '', ' ') ?></span>
                                        <? endif ?>
                                    </span>
                                </span>
                                <? if ($item_hit->count($context->city)): ?>
                                    <span class="dynamicBlock">
                                        <span class="btn_addToCart addCart" data-id="<?= $item_hit->id ?>" data-count="1">
                                            <?=(isset($context->cart_items[$item_hit->id])?'В корзине':'В корзину') ?>
                                        </span>
                                        <!--<span class="btn_buyToClick fastCart" data-id="<?= $item_hit->id ?>">Купить в 1 клик</span>-->
                                    </span>
                                <? else: ?>
                                    <span class="wrapperNone">
                                        <span class="None">Временно нет в наличии</span>
                                    </span>
                                <? endif; ?>
                            </span>
                        </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<? if ($items_sale): ?>
    <section class="slPosition_line">
        <div class="gTitle">Акции</div>
        <div class="homeLine">
            <div class="wrapperSl">
                <div class="owl-carousel-2">
                    <?php foreach ($items_sale as $item_sale): ?>
                        <?php
                        $class = '';
                        if (!$item_sale->count($context->city)) {
                            $class .= ' isNone';
                        }
                        ?>
                            <div class="goodsBlock">
                                <a class="image" href="<?= $item_sale->url() ?>" style="background-image: url(<?= $item_sale->img(true,'item_320') ?>);">
                                    <span class="stickerPosition">
                                      <span class="action">Акция</span>
                                        <? if ($item_sale->isNew): ?>
                                            <span class="new">Новинка</span>
                                        <? endif ?>
                                        <? if ($item_sale->discount): ?>
                                            <span class="discount">-<?= $item_sale->discount ?>%</span>
                                        <? endif ?>
                                    </span>
                                </a>
                            <span class="wrapperPad">
                                <a class="title" href="<?= $item_sale->url() ?>"><span><?= $item_sale->name ?></span></a>
                                <span class="pricePosition">
                                    <span class="text">Цена за 1 <?= ($item_sale->measure_price) ? 'шт' : 'кг' ?>.</span>
                                    <span class="price">
                                        <span class="new"><?= number_format($item_sale->real_price(true), 0, '', ' ') ?> Т</span>
                                        <? if ($item_sale->old_price): ?>
                                            <span class="old"><?= number_format($item_sale->old_price, 0, '', ' ') ?></span>
                                        <? endif ?>
                                    </span>
                                </span>
                                <? if ($item_sale->count($context->city)): ?>
                                    <span class="dynamicBlock">
                                        <span class="btn_addToCart addCart" data-id="<?= $item_sale->id ?>" data-count="1">
                                            <?=(isset($context->cart_items[$item_sale->id]) ? 'В корзине' : 'В корзину') ?>
                                        </span>
                                        <!--<span class="btn_buyToClick fastCart" data-id="<?= $item_sale->id ?>">Купить в 1 клик</span>-->
                                    </span>
                                <? else: ?>
                                    <span class="wrapperNone">
                                        <span class="None">Временно нет в наличии</span>
                                    </span>
                                <? endif; ?>
                            </span>
                            </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
<? endif ?>
<ul class="informLine double padSpace">
    <li class="one">
        <div class="table">
            <img src="<?= $context->AppAsset->baseUrl ?>/images/informLine/1.png" alt="" />
            <span>Бесплатная доставка</span></div>
    </li>
    <li class="two">
        <div class="table">
            <img src="<?= $context->AppAsset->baseUrl ?>/images/informLine/2.png" alt="" />
            <span>Возврат товара, <br /> если не понравилось качество</span></div>
    </li>
    <li class="three">
        <div class="table">
            <img src="<?= $context->AppAsset->baseUrl ?>/images/informLine/3.png" alt="" />
            <span>Порезанный и почищенный продукт</span></div>
    </li>
    <li class="four">
        <div class="table">
            <img src="<?= $context->AppAsset->baseUrl ?>/images/informLine/4.png" alt="" />
            <span>Оплата в момент доставки</span></div>
    </li>
    <li class="five">
        <div class="table">
            <img src="<?= $context->AppAsset->baseUrl ?>/images/informLine/5.png" alt="" />
            <span>Бонус за каждую покупку</span></div>
    </li>
</ul>
<? if ($recipes): ?>
    <section class="slPosition_line" style="background-image: url(<?= $context->AppAsset->baseUrl ?>/images/wave.jpg);">
        <div class="gTitle">Лучшие рецепты с морепродуктами</div>
        <div class="preTitle">Мы выбрали для вас самые интересные и необычные</div>
        <div class="homeLine Article">
            <div class="wrapperSl">
                <div class="owl-carousel-3">
                    <? foreach($recipes as $recipe): ?>
                            <div class="articleBlock">
                                <a href="<?= Url::to(['site/recipe','id'=>$recipe->id]) ?>" class="image" style="background-image: url(<?=$recipe->img(true,'mini_list_recipe')?>);">
                                    <? if ($recipe->isDay): ?>
                                        <div class="stickerPosition">
                                            <div class="popular">Рецепт дня</div>
                                            <div class="best"></div>
                                        </div>
                                    <? endif ?>
                                </a>
                                <div class="wrapperText">
                                    <a href="<?= Url::to(['site/recipe','id'=>$recipe->id]) ?>" class="title"><span><?=$recipe->name?></span></a>
                                    <? if ($recipe->small_body): ?>
                                        <span class="desc">
                                            <span><?= $recipe->small_body ?></span>
                                        </span>
                                    <? endif ?>
                                    <? if ($recipe->time_cooking): ?>
                                        <span class="time">Время приготовления: <?= $recipe->time_cooking ?></span>
                                    <? endif ?>
                                </div>
                            </div>
                    <? endforeach; ?>
                </div>
            </div>
        </div>
        <div class="socialBlock">
            <div id="fb-root"></div>
			<script async defer crossorigin="anonymous" src="https://connect.facebook.net/ru_RU/sdk.js#xfbml=1&version=v6.0"></script>
            <!--<script>(function (d, s, id) {
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)) return;
                    js = d.createElement(s);
                    js.id = id;
                    js.src = "//connect.facebook.net/ru_RU/sdk.js#xfbml=1&version=v2.4";
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));</script>
			 <div class="fb-like" data-href="https://developers.facebook.com/docs/plugins/" data-layout="button" data-action="like" data-show-faces="true" data-share="true"></div>-->
			<div class="fb-like" data-href="https://site.ru/" data-width="320px" data-layout="standard" data-action="like" data-size="small" data-share="true"></div>
        </div>
    </section>
<? endif ?>
<? if ($brands): ?>
    <section class="slPosition_line" style="background-image: url(<?= $context->AppAsset->baseUrl ?>/images/wave.jpg);">
        <div class="gTitle">Наши партнеры</div>
        <div class="preTitle"></div>
        <div class="homeLine Article">
            <div class="wrapperSl">
                <div class="owl-carousel-1">
                    <?foreach($brands as $brand): ?>
                            <div class="articleBlock">
                                <a href="<?= Url::to(['site/brands','id'=>$brand->id]) ?>" class="image" style="background-image: url(<?=$brand->img?>);">
       
                                </a>
                                <div class="wrapperText">
                                    <a href="<?= Url::to(['site/brands','id'=>$brand->id]) ?>" class="title"><span><?=$brand->name?></span></a>
                                </div>
                            </div>
                    <? endforeach; ?>
                </div>
            </div>
        </div>
    </section>
<? endif ?>
<main class="aboutCompany padSpace">
    <!--<div class="gTitle">Добро пожаловать на сайт SITE.RU</div>-->
	<h1 class="gTitle">Интернет-магазин неких товаров SITE.RU</h1>
    <div class="text">
        <?= $context->settings->get('index_text') ?>
    </div>
    <div class="intro-links">
        <a class="intro-link appstore" href="https://apps.apple.com/us/app/SITE.RU-ru/id1550847463">
            <?=Html::img($context->AppAsset->baseUrl.'/images/appstore.png', ['alt'=>''])?>
        </a>
        <a class="intro-link playmarket" href="https://play.google.com/store/apps/details?id=com.SITE.RUshop">
            <?=Html::img($context->AppAsset->baseUrl.'/images/playmarket.png', ['alt'=>''])?>
        </a>
    </div>
</main>
<section class="subscribePosition padSpace">
    <?php
    $model = new Subscription();
    $form = ActiveForm::begin([
        'action' => Url::to(['site/send-form', 'f' => 'subs']),
        'enableAjaxValidation' => false,
        'options' => ['enctype' => 'multipart/form-data', 'class' => 'formSubscribe'],
        'fieldClass' => ActiveField::className(),
        'fieldConfig' => [
            'options' => ['class' => 'string'],
            'template' => <<<HTML
{input}<button class="btn_formSubscribe" type="submit">Подписаться</button>
HTML
            ,
        ]
    ]); ?>
	
	
	
	
	
        <div class="string">
            <label>Узнавайте о скидках первыми! Подпишитесь на нашу рассылку</label>
        </div>
        <?= $form->field($model, 'email'); ?>

        <?php ActiveForm::end(); ?>

</section>
<?
$this->registerJsFile(
    $context->AppAsset->baseUrl . '/js/sliders.js',
    [
        'depends' => [
            '\frontend\assets\AppAsset'
        ]
    ]
);
$this->registerJs(<<<JS


$('.owl-carousel-0').owlCarousel({
  loop:true,
  margin:0,
  items: 1,
  nav:false,
  dots: true,
  autoplay: true,
  autoplayTimeout: 5000,
    autoplayHoverPause: true
});


$('.owl-carousel-1').owlCarousel({
  loop:true,
  margin:25,
  nav:true,
  dots: false,
  responsive:{
      0:{
          items:1,
          slideBy: 1
      },
      767:{
          items:2,
          slideBy: 2
      },
      1000:{
          items:4,
          slideBy: 4
      },
      1500: {
          items: 5,
          slideBy: 5
      }
  },
  navText: ['', ''],
  autoplay: false,
  autoplayTimeout: 5000,
    autoplayHoverPause: true
});

$('.owl-carousel-2').owlCarousel({
  loop:true,
  margin:25,
  nav:true,
  dots: false,
  responsive:{
      0:{
          items:1,
          slideBy: 1
      },
      767:{
          items:1,
          slideBy: 1
      },
      1000:{
          items:4,
          slideBy: 4
      },
      1500: {
          items: 5,
          slideBy: 5
      }
  },
  navText: ['', ''],
  autoplay: false,
  autoplayTimeout: 5000,
    autoplayHoverPause: true
});

$('.owl-carousel-3').owlCarousel({
  loop:true,
  margin:25,
  nav:true,
  dots: false,
  responsive:{
      0:{
          items:1,
          slideBy: 1
      },
      767:{
          items:1,
          slideBy: 1
      },
      1000:{
          items:2,
          slideBy: 2
      },
      1500: {
          items: 3,
          slideBy: 3
      }
  },
  navText: ['', ''],
  autoplay: true,
  autoplayTimeout: 5000,
    autoplayHoverPause: true
});


if ($(window).width() > 1500) {

    if ($('.owl-carousel-2 .owl-stage .owl-item').length < 5) {
        $('.owl-carousel-2').find('.owl-controls').css('display', 'none');
    } else {
        $('.owl-carousel-2').find('.owl-controls').css('display', '');
    }

} else {
$('.owl-carousel-2').find('.owl-controls').css('display', '');
}


$(window).resize(function() {
if ($(this).width() > 1500) {

    if ($('.owl-carousel-2 .owl-stage .owl-item').length < 5) {
        $('.owl-carousel-2').find('.owl-controls').css('display', 'none');
    } else {
        $('.owl-carousel-2').find('.owl-controls').css('display', '');
    }

} else {
        $('.owl-carousel-2').find('.owl-controls').css('display', '');
    }
});

JS
)
?>