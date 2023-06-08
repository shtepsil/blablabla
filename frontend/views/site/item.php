<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $item \common\models\Items
 * @var $similar_items \common\models\Items[]
 * @var $associated \common\models\Items[]
 * @var $recipes \common\models\Recipes[]
 * @var $counts \common\models\ItemsCount[]
 */
use common\components\Debugger as d;
use common\models\ItemsTogether;
use common\models\ReviewsItem;
use common\models\Sets;
use frontend\form\ReviewItemSend;
use frontend\widgets\ActiveField;
use frontend\widgets\ActiveForm;
use shadow\widgets\ReCaptcha\ReCaptcha;
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\helpers\CHtml;
use shadow\helpers\StringHelper;

$context = $this->context;
$this->registerJsFile(
    $context->AppAsset->baseUrl . '/js/plugins/goods_slide.js',
    [
        'depends' => [
            '\frontend\assets\AppAsset'
        ]
    ]
);
$counts = $item->getItemsCounts()->indexBy('city_id')->where(['city_id' => [$context->city, 1]])->all();
$onlyAlmaty = (
    ($context->city != 1 && isset($counts[1]) && (double)$counts[1]->count) &&
    !(isset($counts[$context->city]) && (double)$counts[$context->city]->count)
);
$isCount = (isset($counts[$context->city]) && (double)$counts[$context->city]->count);
?>
<style>
    /* Иконка для показа/скрытия цены за 1кг/шт */
    .Goods .gPrice2 {
        position: relative;
    }
    .Goods .gPrice2 .fa:not([data-type=balloon]){
        position: absolute;
        top: 10px;
        right: 0;
        font-size: 16px;
        border: 1px solid;
        padding: 5px 8px;
        border-radius: 5px;
        color: rgba(128,133,141,1);
        cursor: pointer;
    }
	.Goods .gPrice2 .balloon-price-kg{
		position: absolute;
		top: -38px;
		right: -24px;
		font: 1.2em/1.33em "Proxima Nova", sans-serif;
		background-color: rgba(0,0,0,1);
		color: white;
		padding: 5px 10px;
		border-radius: 5px;
		z-index: 10;
		text-align: center;
	}
	.Goods .gPrice2 .balloon-price-kg .fa{
		position: absolute;
		bottom: -11px;
		right: 50%;
		width: 5%;
		color: black;
		font-size: 20px;
	}
    @media(max-width: 999px){
        .Goods .gPrice2 .fa:not([data-type=balloon]){
            top: 40px;
        }
        .Goods .gPrice2 .balloon-price-kg{
            top: -10px;
        }
    }
    @media(max-width: 767px){
        .Goods .gPrice2 .fa:not([data-type=balloon]){
            top: 24px;
        }
        .Goods .gPrice2 .balloon-price-kg{
            top: -25px;
        }
    }
    @media(max-width: 380px){
        .Goods .gPrice2 .balloon-price-kg{
            right: -5px;
        }
        .Goods .gPrice2 .balloon-price-kg .fa{
            right: 27%;
        }
    }
</style>
    <div class="breadcrumbsWrapper padSpace">
        <?= $this->render('//blocks/breadcrumbs') ?>
    </div>
    <div <?=$md->get('product','itemscope')?> class="Goods goodsinner padSpace <?= (!$onlyAlmaty && !$isCount) ? 'isNone' : '' ?>">
        <h1 <?=$md->get('product','name')?> class="title"><?= $item->name ?></h1>
        <?d::res()?>
        <? if ($item->body_small): ?>
            <div class="goodsDescription_short">
                <?=$item->body_small?>
            </div>
        <? endif ?>
        <div class="goodsTopLine">
            <? if ($item->brand_id): ?>
				<div class="country"><?= $item->brand->name ?></div>
                <div class="country">Страна: <?= $item->brand->country ?></div>		
            <? endif ?>
            <? if ($item->article): ?>
                <div class="artikel">Артикул: <?= $item->article ?></div>
            <? endif ?>
            <? if ($onlyAlmaty): ?>
                <div class="available">Нет в вашем городе</div>
            <? else: ?>
                <? if ($isCount): ?>
                    <div class="available">В наличии</div>
                <? else: ?>
                    <div class="available">Нет в наличии</div>
                <? endif; ?>
            <? endif; ?>
			<div class="wrapperRating">
			<?if($item->popularity > 0):?>
                <div <?=$md->get('aggregateRating','itemscope')?> class="Rating">
                    <?=$md->get('aggregateRating','meta')?>
            <?else:?>
                <div class="Rating">
            <?endif?>
                <div class="star<?=($item->popularity>0)?' check':''?>">
                    <div class="star<?=($item->popularity>1)?' check':''?>">
                        <div class="star<?=($item->popularity>2)?' check':''?>">
                            <div class="star<?=($item->popularity>3)?' check':''?>">
                                <div class="star<?=($item->popularity>4)?' check':''?>"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <span class="reviews" data-goto="reviews"><?= Yii::t('main','count_reviews',['n'=>count($reviews)]) ?></span>
            </div>        	
        </div>
        <div class="goodsPosition">
            <div class="gImage">
                <?php
                $imgs = $item->img(false, 'page_item', true); 
                $item_img_params = [
                    'alt'=>StringHelper::clearHtmlString($item->body)
                ];
                $srcset = [];
                $img_microdata = []; 
                $j = 0;
                ?>
                <? if ($imgs): ?>
                    <?php
                    if(count($arr_srcset_imgs = $item->seoImg(Yii::$app->seo->resizes_imgs))){
                        foreach($arr_srcset_imgs as $key=>$img){
                            $srcset[$key] = '';
                            if(is_array($img)){
                                foreach($img as $i_key=>$img_path){
                                    $arr_key = explode('_',$i_key);
                                    $srcset[$key] .= $img_path.' '.$arr_key[1].'w, ';
                                    if($j == 0 AND $img_path != ''){
                                        $img_microdata[] = $img_path;
                                    }
                                }
                                $srcset[$key] = substr($srcset[$key],0,-2);
                            }
                            $j++;
                        }
                        $item_img_params['srcset'] = $srcset[0];
						if(count($img_microdata)){
							echo $md->getImagesLink($img_microdata);
						}
                    }
                    ?>
					<div <?=$md->get('imageObject','itemscope')?> class="image" style="background-image: url(<? $imgs[0] ?>);" title="<?= $item->name ?>"> 
                        <?php
						echo $md->get('imageObject','meta');
						$item_img_params['itemprop'] = 'contentUrl';
						?>
                        <?=Html::img($imgs[0],$item_img_params)?>
                        <? if ($item->old_price || $item->discount || $item->isNew): ?>
                            <span class="stickerPosition">
                                <? if ($item->old_price || $item->discount): ?>
                                    <span class="action">Акция</span>
                                <? endif ?>
                                <? if ($item->isNew): ?>
                                    <span class="new">Новинка</span>
                                <? endif ?>
                                <? if ($item->discount): ?>
                                    <span class="discount">-<?= $item->discount ?>%</span>
                                <? endif ?>
                            </span>
                        <? endif ?>
                    </div>
                    <ul class="image_mini">
						<?if($imgs AND (count($imgs) > 1)):?>
							<?php foreach ($imgs as $key => $img): ?>
								<li <?= ($key == 0) ? 'class="current"' : '' ?> data-preview="<?= $img ?>" style="background-image: url(<?= $img ?>);" data-type="image" data-srcset="<?=$srcset[$key]?>"></li>
							<?php endforeach; ?>
                        <?endif?>
                        <? if ($item->video): ?>
                            <?
                            $id_video = '';
                            $video_url = parse_url($item->video);
                            if (isset($video_url['query'])) {
                                parse_str($video_url['query'], $video_params);
                                if (isset($video_params['v'])) {
                                    $id_video = $video_params['v'];
                                }
                            }
                            ?>
                            <? if ($id_video): ?>
                                <li data-type="video">
                                    <iframe src="https://www.youtube.com/embed/<?= $id_video ?>" frameborder="0" allowfullscreen></iframe>
                                </li>
                            <? endif ?>
                        <? endif ?>
                    </ul>
                <? endif ?>
            </div>
            <div <?=$md->get('offers','itemscope')?> class="gSelect">

                <? if ($onlyAlmaty): ?>
                    <?=$md->get('offers',['availability'=>'InStock'])?>
                <? else: ?>
                    <? if ($isCount): ?>
                        <?=$md->get('offers',['availability'=>'InStock'])?>
                    <? else: ?>
                        <?=$md->get('offers',['availability'=>'PreOrder'])?>
                    <? endif; ?>
                <? endif; ?>

                <?=$md->get('offers','meta')?>

                <form class="gSelectWrapper">
                    <div class="gNumbers">
                        <? if (true || $item->measure_price == $item->measure): ?>
                            <span><?= ($item->measure) ? 'шт' : 'кг' ?>.</span>
                        <? else: ?>
                            <? if ($item->measure_price == 1): ?>
                                <span><?= (double)$item->weight . ' кг' ?>.</span>
                            <? else: ?>
                                <span><?= (double)$item->weight . ' кг' ?>.</span>
                            <? endif; ?>
                        <? endif; ?>
                        <div class="inputWrapper" id="count_item_to_cart">
                            <div class="btnMinus"></div>
                            <div class="btnPlus"></div>
                            <input type="text" value="1" readonly data-type="<?= $item->measure ?>" data-id="<?= $item->id ?>" />
                        </div>
                    </div>
                    <?
                    $view_price_kg = false;
                    if ($item->measure_price != $item->measure && $item->weight != 1){
                        $view_price_kg = true;
                    }

                    $measure_price = ($item->measure_price) ? 'шт' : 'кг';

                    ?>

                    <div class="gPrice gPrice1 <?=($view_price_kg)?'dn':''?>">
                        <div class="num">Цена за 1 <?= $measure_price ?></div>
                        <div class="price">
                            <div class="new"><?= number_format($item->real_price(), 0, '', ' ') ?> тг.</div>
                            <? if ($item->old_price): ?>
                                <div class="old"><?= $item->old_price ?></div>
                            <? endif ?>
                        </div>
                    </div>

                    <?
                    /*
                     * Если "Вид расчёта(Вразвес/Поштучно)" не равен "Ед.измерения(кг/шт)"
                     * и вес больше 1
                     */
                    if ($view_price_kg): ?>
                        <?
                        $weight = $item->weight;
                        $weight_str = ' кг';
                        if (is_float($weight) && $weight < 1) {
                            $weight = $weight * 1000;
                            $weight_str = ' гр.';
                        }
                        ?>
                        <div class="gPrice gPrice2">
                            <div class="num">Цена за <?= $weight . $weight_str ?></div>
                            <div class="price">
                                <div class="new"><?= number_format($item->sum_price($count = 1, $type = 'main', $price = 0, $weight = 0), 0, '', ' ') ?> тг.</div>
                                <? if ($item->old_price): ?>
                                    <div class="old"><?= $item->sum_price(1, 'main', $item->old_price) ?></div>
                                <? endif ?>
                            </div>
                            <i class="fa fa-caret-down" aria-hidden="true" title="Показать цену за 1<?= $measure_price ?>" data-type="open"></i>
                            <i class="fa fa-caret-up dn" aria-hidden="true" data-type="close"></i>
                            <div class="balloon-price-kg">
                                <i class="fa fa-caret-down" aria-hidden="true" data-type="balloon"></i>
                                Показать <br>
                                цену за 1кг
                            </div>
                        </div>
                    <? endif ?>
                    <?
                    $percent_bonus = $context->function_system->percent();
                    $full_bonus_item = floor(($item->real_price() * ($percent_bonus)) / 100);
                    ?>
                    <div class="num_bon_si">
                        <b><?=$full_bonus_item?></b> бонусов за этот товар
                    </div>
                    <? if (!$onlyAlmaty && !$isCount): ?>
                        <div class="btn_addToCart fastCart" data-id="<?= $item->id ?>" data-count="1">Оформить предзаказ</div>
                        <!--<div class="order_inform">
                            Оформив предзаказ,
                            вы получите скидку
                            10% на этот товар
                        </div>-->
                    <? else: ?>
                        <div class="btn_addToCart addCart" data-id="<?= $item->id ?>" data-count="1" data-text="Добавить в корзину">
                            <?= (isset($context->cart_items[$item->id]) ? 'В корзине' : 'Добавить в корзину') ?>
                        </div>
                        <!--<div class="btn_buyToClick fastCart" data-id="<?= $item->id ?>">Купить в 1 клик</div>-->
                    <? endif; ?>
                </form>
                <div class="socialPosition">
                    <script type="text/javascript" src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
                    <script type="text/javascript" src="//yastatic.net/share2/share.js" charset="utf-8"></script>
                    <div class="ya-share2" data-services="vkontakte,facebook,twitter,odnoklassniki,moimir"></div>
                </div>
            </div>
            <ul class="gInform">
                <li>
                    <img src="<?= $context->AppAsset->baseUrl ?>/images/informLine/icon_grey_5.png" alt="" style="width:70px;">
                    <?
                    $percent_bonus = $context->function_system->percent();
                    $full_bonus_item = floor(($item->real_price() * ($percent_bonus)) / 100);
                    ?>
                    <p>За товар получите от <b><?=$full_bonus_item?></b> бонусов</p>
                </li>
                <li>
                    <img src="<?= $context->AppAsset->baseUrl ?>/images/informLine/icon_grey_1.png" alt="" />
                    <p><?= $delivery ?></p>
                </li>
                <li>
                    <img src="<?= $context->AppAsset->baseUrl ?>/images/informLine/icon_grey_2.png" alt="" />
                    <p>Возврат товара, если не понравилось качество</p>
                </li>
                <li>
                    <img src="<?= $context->AppAsset->baseUrl ?>/images/informLine/icon_grey_3.png" alt="" />
                    <p>Порезанный и почищенный продукт</p>
                </li>
                <li>
                    <img src="<?= $context->AppAsset->baseUrl ?>/images/informLine/icon_grey_4.png" alt="" />
                    <p>Оплата в момент доставки</p>
                </li>
            </ul>
            <div class="clear"></div>
            <?
            $active = false;
            $li_tab = '';
            $li_body = '';
            $li_body_mobile = '';
            $tabs = [
                'body' => 'Описание',
                'feature' => 'Характеристики',
                'storage' => 'Условия хранения',
                'delivery' => 'Доставка и оплата',
            ];
            foreach ($tabs as $key => $value) {
                if ($item->{$key}) {
                    $val_tab = $item->{$key};
                    $li_tab .= Html::tag('li', $value, ['class' => (!$active) ? 'current' : null]);
                    $li_body .= Html::tag('li', "<div itemprop=\"description\" class=\"textInner\">$val_tab</div>", ['class' => (!$active) ? 'current' : null]);
                    $li_body_mobile .= Html::tag('li', "<span>$value</span><div class=\"tBody\"><div class=\"textInner\">$val_tab</div></div>", ['class' => (!$active) ? 'current' : null]);
                    $active = true;
                }
            }
            $li_tab .= Html::tag('li', 'Отзывы <i>(' . count($reviews) . ')</i>', ['class' => (!$active) ? 'current scReviews' : 'scReviews']);
            if ($reviews) {
                $review_body_list = $this->render('//blocks/reviews_list', [
                    'reviews' => $reviews,
                    'md' => $md,
                ]);
            } else {
                $review_body_list = '';
            }
            if (Yii::$app->user->isGuest) {
                $review_body = $review_body_mobile = <<<HTML
<div class="lineText">
	<p>Чтобы добавить отзыв, Вы должны
		<a href="#" onclick="popup({block_id: '#popupEntreg', action: 'open', position_type: 'absolute'})">авторизоваться</a>
		на сайте.
	</p>
</div>
HTML;
            } else {
                $review_body = $this->render('//blocks/_form_reviews', ['item' => $item]);
                $review_body_mobile = $this->render('//blocks/_form_reviews', ['item' => $item]);
            }
            $review_body .= $review_body_list;
            $review_body_mobile .= $review_body_list;
            $li_body .= Html::tag('li', "<div class=\"listReviews\">{$review_body}</div>", ['class' => (!$active) ? 'current' : '']);
            $text_mobile_review = 'Отзывы <i>(' . count($reviews) . ')</i>';
            $li_body_mobile .= Html::tag(
                'li',
                "<span>$text_mobile_review</span><div class=\"tBody\">$review_body_mobile</div>",
                ['class' => (!$active) ? 'current scReviews_mob' : 'scReviews_mob']
            );
            $active = true;
            if ($recipes) {
                $li_tab .= Html::tag('li', 'Рецепты', ['class' => (!$active) ? 'current' : null]);$recipes_body = $this->render('//blocks/recipes_list', [
                    'recipes' => $recipes,
                    'md' => $md,
                ]);
                $li_body .= Html::tag('li', "<div class=\"Article articlerecipe\">{$recipes_body}</div>", ['class' => (!$active) ? 'current' : null]);
                $li_body_mobile .= Html::tag('li', "<span>Рецепты</span><div class=\"tBody\"><div class=\"Article articlerecipe\">{$recipes_body}</div></div>",
                    ['class' => (!$active) ? 'current' : null]);
            }
            ?>
            <div class="tabInterface mobile" data-type="tabs">
                <ul class="tabHead" data-type="thead">
                    <?= $li_body_mobile ?>
                </ul>
            </div>
            <div class="tabInterface desktop" data-type="tabs">
                <ul class="tabHead" data-type="thead">
                    <?= $li_tab ?>
                </ul>
                <ul class="tabBody" data-type="tbody">
                    <?= $li_body ?>
                </ul>
            </div>
            <? if ($context->city == 1 || true): ?>
                <?
                /**
                 * @var $togethers_items ItemsTogether[]
                 */
                $a_togethers_items[] = $item->getItemsTogethers()->with('item')->all();
                $count_item = 0;
                $sets_string = '';
                if ($a_togethers_items) {
                    foreach ($a_togethers_items as $togethers_items) {
                        $no_visible = !($togethers_items);
                        $add_string_set = '';
                        $img_discount = '';
                        $full_price_set = $full_price = 0;
                        $count_togethers = count($togethers_items);
                        foreach ($togethers_items as $i=> $together_item) {
                            if ($together_item->item->isVisible) {
                                $class_together = '';
                                if(($i+1)==$count_togethers){
                                    $class_together = 'last_goods';
                                }
                                $count_item = (double)$together_item->count;
                                $full_price_set += $together_item->real_price($count_item);
                                $full_price += $together_item->item->sum_price($count_item);
                                $price_str = number_format($together_item->item->sum_price($count_item), 0, '', ' ');
                                $string_this = ($together_item->item->id == $item->id) ? 'Этот товар' : ' ';
//                                $measure = ($together_item->item->measure_price) ? 'шт' : 'кг';
                                $measure = 'шт';
                                $url_item_discount = $together_item->item->url();
                                $add_string_set .= <<<HTML
<li class="{$class_together}">
	<a href="{$url_item_discount}" class="Discount_goods">
		<span class="d_image" style="background-image: url({$together_item->item->img()});"></span>
		<span class="d_wrap_hidden">
            <span class="d_title">{$together_item->item->name}</span>
            <span class="d_desc_text">{$together_item->item->body_small}</span>
        </span>
            <span class="d_price">{$price_str} т.</span>
	</a>
</li>
HTML;
                            } else {
                                $no_visible = true;
                                break;
                            }
                        }

                        if (!$no_visible) {
                            $price_set = number_format($full_price_set, 0, '', ' ');
                            $sale_full_price = number_format(round($full_price - $full_price_set), 0, '', ' ');
                            $string_full_price = number_format($full_price, 0, '', ' ');
                            $add_string_set .= <<<HTML
<li>
	<div class="blockControl_panel">
		<table>
			<tr>
				<td>Товаров на сумму</td>
				<td><b>{$string_full_price} т.</b></td>
			</tr>
			<tr>
				<td>Экономия</td>
				<td><span class="green"><b>{$sale_full_price} т.</b></span></td>
			</tr>
			<tr>
				<td><b>Ваша сумма</b></td>
				<td><span class="big"><b> {$price_set} т.</b></span></td>
			</tr>
			<tr>
				<td colspan="2">
					<button class="btn_addToCart add_discount" data-id-item="{$item->id}">Купить набор</button>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="btn_buyToClick fast_discount" data-id-item="{$item->id}">Купить набор в 1 клик</div>
				</td>
			</tr>
		</table>
	</div>
</li>

HTML;
                            $sets_string = Html::tag('ul', $add_string_set, ['class' => 'D_list']);

                        }
                    }
                }
                ?>
                <? if ($sets_string): ?>
                    <form class="Discount">
                        <div class="title">Купи в наборе со скидкой</div>
                        <?= $sets_string ?>
                    </form>
                <? endif ?>
            <? endif; ?>
        </div>
    </div>
<? if ($associated): ?>
    <div class="Goods goodslist padSpace">
        <div class="bTitle">Рекомендуем</div>
        <div class="goodsBlocks" data-check="height">
            <?= $this->render('//blocks/items', ['items' => $associated]) ?>
        </div>
    </div>
<? endif ?>
    <ul class="informLine double padSpace">
        <li class="one">
            <div class="table">
                <img src="<?= $context->AppAsset->baseUrl ?>/images/informLine/1.png" alt="" />
                <span><?=$delivery?></span></div>
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
<?php
$url_cart = Url::to(['site/cart']);
$this->registerJsFile($context->AppAsset->baseUrl . '/js/plugins/goods_slide.js', ['depends' => 'frontend\assets\AppAsset']);
$this->registerJs(<<<JS
//JS
setTimeout(function(){
	$('.gPrice2 .balloon-price-kg').fadeOut(100);
}, 3000);
$('.gPrice2 .fa').on('mouseover', function(){
	var tthis = $(this),
		wrap = $('.gPrice2'),
		gPrice1 = $('.gPrice1');
	if(!gPrice1.is(':visible')){
		wrap.find('.balloon-price-kg').fadeIn(100);
	}
}).on('mouseout', function(){
	var tthis = $(this),
		wrap = $('.gPrice2'),
		gPrice1 = $('.gPrice1');
	
	wrap.find('.balloon-price-kg').fadeOut(100);
});
$('.gPrice2 .fa').on('click', function(){
	var tthis = $(this),
		wrap = $('.gPrice2'),
		gPrice1 = $('.gPrice1');
	if(tthis.attr('data-type') == 'open'){
		gPrice1.slideDown(100);
		wrap.find('.balloon-price-kg').fadeOut(100);
		wrap.find('.fa[data-type=open]').hide(10, function(){
			wrap.find('.fa[data-type=close]').show(10);
		});
	}else{
		gPrice1.slideUp(100);
		wrap.find('.fa[data-type=close]').hide(10, function(){
			wrap.find('.fa[data-type=open]').show(10);
		});
	}
});
goods_inner_slide();
$('#count_item_to_cart').on('click', '.btnPlus', function (e) {
    var inp = $('input','#count_item_to_cart');
    var inpVal = $(inp).val();
    var measure = $(inp).data('type');
    var id = $(inp).data('id');
    if (typeof measure == 'undefined' || measure == 1) {
        $(inp).val(+inpVal + 1);
    } else if (measure == 0) {
        var float = /^(\d+\.0)$/;
        var val = parseFloat(+inpVal) + 0.1;
        val = val.toFixed(1);
        if (float.test(val)) {
            val = parseInt(val);
        }
        $(inp).val(val);
    }
    $('.addCart[data-id=' + id + ']').data('count', $(inp).val());
    $('.fastCart[data-id=' + id + ']').data('count', $(inp).val());
}).on('click', '.btnMinus', function (e) {
    var inp = $('input','#count_item_to_cart');
    var inpVal = $(inp).val();
    var measure = $(inp).data('type');
    var id = $(inp).data('id');
    if (typeof measure == 'undefined' || measure == 1) {
        if (inpVal > 1) {
            $(inp).val(+inpVal - 1);
        }
    } else if (measure == 0) {
        if (inpVal > 0.1) {
            var float = /^(\d+\.0)$/;
            var val = parseFloat(+inpVal) - 0.1;
            val = val.toFixed(1);
            if (float.test(val)) {
                val = parseInt(val);
            }
            $(inp).val(val);
        }
    }
    $('.addCart[data-id=' + id + ']').data('count', $(inp).val());
    $('.fastCart[data-id=' + id + ']').data('count', $(inp).val());
}).on('change','input',function(e){
    var measure = $(this).data('type');
    var val = $(this).val();
    var inpVal = $(this).val();
    var id = $(this).data('id');
    if (typeof measure == 'undefined' || measure == 1) {
        if (inpVal > 1) {
            var float_no = /^(\d+\.\d+)$/;
            if (float_no.test(val)) {
                val = parseInt(val);
                $(this).val(val);
            }
        }else{
            val = 1;
            $(this).val(val);
        }
    } else if (measure == 0) {
        if (inpVal > 0.1) {
            var float = /^(\d+\.0)$/;
            val = parseFloat(+inpVal);
            val = val.toFixed(1);
            if (float.test(val)) {
                val = parseInt(val);
                $(this).val(val);
            }
        }else{
            val = 0.1;
            $(this).val(val);
        }
    }
    $('.addCart[data-id=' + id + ']').data('count', $(this).val());
    $('.fastCart[data-id=' + id + ']').data('count', $(this).val());
});
JS
)
?>