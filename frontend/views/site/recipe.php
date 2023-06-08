<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $item \common\models\Recipes
 *
 */
use common\models\RecipesItem;
use common\models\RecipesMethod;
use yii\helpers\Url;
use yii\helpers\Html;

$context = $this->context;

/*
 * Удаляем всё кроме чисел
 * Временные меры, после переделки админки,
 * нужно будет тут исправить
 */
$arr_tc = explode(':',$item->time_cooking);
$cookTime = $arr_tc[0];
$prepTime = $arr_tc[1];

?>
    <div class="breadcrumbsWrapper padSpace">
        <?= $this->render('//blocks/breadcrumbs') ?>
    </div>
    <div class="Recipe padSpace">
        <h1 class="Title"><?= $item->name ?></h1>
        <div id="recipeSlider" class="recipeSlider">
            <? if ($item->isDay): ?>
                <div class="recipeDay">Рецепт дня</div>
            <? endif ?>
            <!--        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel" data-interval="10000">-->
            <!-- Indicators -->
            <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                <!--<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>-->
            </a>
            <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                <!--<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>-->
            </a>
            <?
            $imgs = $item->recipesImgs;
            $item_img_params = [];
            $srcset = [];
            $img_microdata = [];
            $j = 0;
            if(count($arr_srcset_imgs = $item->seoImg(Yii::$app->seo->resizes_imgs))){
                foreach($arr_srcset_imgs as $key=>$img){
                    $srcset[$key] = '';
                    if(is_array($img)){
                        foreach($img as $i_key=>$img_path){
                            $arr_key = explode('_',$i_key);
                            $srcset[$key] .= $img_path.' '.$arr_key[1].'w, ';
                            if($j == 0){
                                if($img_path != '')
                                    $img_microdata[] = $img_path;
                            }
                        }
                        $srcset[$key] = substr($srcset[$key],0,-2);
                    }
                    $j++;
                }
            }

            ?>
            <!-- Wrapper for slides -->
            <div class="carousel-inner owl-carousel-0" role="listbox">
                <? foreach ($imgs as $key => $img_recipe):
                    $item_img_params['srcset'] = $srcset[$key];
                    ?>
                    <div class="item<?= ($key == 0) ? ' active' : '' ?>" style="background-image: url(<?=$img_recipe->url ?>);">
                        <?=Html::img($img_recipe->url,$item_img_params)?>
                    </div>
                <? endforeach; ?>
            </div>
            <!--        </div>-->
        </div>
        <div class="recipeColumn">
            <div class="leftCol">
                <div class="inTitle">Ингредиенты</div>
                <table class="ingredients">
                    <?
                    /**
                     * @var $ingredients RecipesItem[]
                     */
                    $ingredients = $item->getRecipesItems()->with('item')->all();
                    $recipeIngredient = '[';
                    ?>
                    <? foreach ($ingredients as $ingredient):
                        // Строка для microdata
                        $recipeIngredient .= '"'.$ingredient->name.' '.$ingredient->count.'",';
                        ?>
                        <tr>
                            <td>
                                <? if ($ingredient->item_id && $ingredient->item->isVisible): ?>
                                    <a href="<?= $ingredient->item->url() ?>"><?= $ingredient->name ?></a>
                                <? else: ?>
                                    <?= $ingredient->name ?>
                                <? endif; ?>
                            </td>
                            <td><?= $ingredient->count ?></td>
                        </tr>
                    <? endforeach;
                    if($recipeIngredient != '['){
                        $recipeIngredient = substr($recipeIngredient,0,-1);
                    }
                    $recipeIngredient .= ']';
                    ?>
                </table>
            </div>
            <div class="rightCol">
                <div class="blockTimeEnd">
                    <div class="top_T_E">
                        <div class="timeEnd">
                            <p>Время приготовления</p>
                            <p><b><?= $item->time_cooking ?></b></p>
                        </div>
                        <?if($item->description_time_cooking):?>
                            <div class="description-time-cooking">
                                <p><?=$item->description_time_cooking?></p>
                            </div>
                        <?endif?>
                    </div>
                    <div class="bottom_T_E">
                        <a href="#use_recipes" class="btn_Form blue">Купить необходимые продукты</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="Recipe graphics padSpace">
        <div class="inRecipe">
            <div class="Title">Способ приготовления:</div>
            <ul class="listRecipe">
                <?
                /**@var $methods RecipesMethod[] */
                $methods = $item->getRecipesMethods()->orderBy(['sort' => SORT_ASC])->all();
                $recipeInstructions = '[';
                ?>
                <? foreach ($methods as $key => $method):
                    // Для microdata
                    $recipeInstructions .= '{"@type": "HowToStep","name":"'.$method->name.'","text": "'.$method->body.'"},';
                    ?>
                    <li>
                        <? if ($method->img): ?>
                            <div class="image" style="background-image: url(<?= $method->img ?>);"></div>
                        <? endif ?>
                        <div class="description">
                            <div class="top_description">
                                <div class="num"><?= ++$key ?></div>
                                <span><?= $method->name ?></span>
                            </div>
                            <div class="body_description">
                                <div class="text">
                                    <?= $method->body ?>
                                </div>
                            </div>
                        </div>
                    </li>
                <? endforeach;
                if($recipeInstructions != '['){
                    $recipeInstructions = substr($recipeInstructions,0,-1);
                }
                $recipeInstructions .= ']';
                ?>
            </ul><?php
            $m_images = '';
            if(count($img_microdata)){
                $m_images .= '[';
                foreach($img_microdata as $mi){
                    $m_images .= '"'.$mi.'",';
                }
                if($m_images != '['){
                    $m_images = substr($m_images,0,-1).']';
                }
            }else{
                $m_images = '"'.Yii::$app->params['no_image'].'"';
            }
            $keywords = ($seo['keywords'])?:$item->name;
            $description = ($seo['description'])?:$item->small_body;
            $author_name = Yii::$app->params['siteNameWithDomain'];

            //...
            $script_microdata = <<< JS
    {
      "@context": "https://schema.org/",
      "@type": "Recipe",
      "name": "{$item->name}",
      "image": {$m_images},
      "description": "{$description}",
      "keywords": "{$keywords}",
      "author": {
        "@type": "Person",
        "name": "{$author_name}"
      },
      "prepTime": "PT{$prepTime}M",
      "cookTime": "PT{$cookTime}H",
      "totalTime": "",
      "nutrition": {
        "@type": "NutritionInformation",
        "calories": ""
      },
      "recipeIngredient": {$recipeIngredient},
      "recipeInstructions": {$recipeInstructions}
    }
JS;

            echo Html::script($script_microdata,['type'=>'application/ld+json']);


            ?></div>
    </div>
<?
/**
 * @var $goods \common\models\Items[]
 */
$goods = $item->getItems()->andWhere(['isVisible' => 1])->all();
?>
<? if ($goods): ?>
    <div class="Goods goodsrecipe bgWave padSpace" id="use_recipes">
        <div class="mTitle">В рецепте использованы:</div>
        <div class="goodsBlocks">
            <? foreach ($goods as $good): ?>
                <?php
                $class = '';
                if (!$good->count($context->city)) {
                    $class .= ' isNone';
                }
                ?>
                <a href="<?= Url::to(['site/item', 'id' => $good->id]) ?>" class="goodsBlock<?= $class ?>">
                    <span class="image" style="background-image: url(<?= $good->img() ?>);">
                        <? if ($good->old_price || $good->discount || $good->isNew): ?>
                            <span class="stickerPosition">
                                <? if ($good->old_price || $good->discount): ?>
                                    <span class="action">Акция</span>
                                <? endif ?>
                                <? if ($good->isNew): ?>
                                    <span class="new">Новинка</span>
                                <? endif ?>
                                <? if ($good->discount): ?>
                                    <span class="discount">-<?= $good->discount ?>%</span>
                                <? endif ?>
                            </span>
                        <? endif ?>
                    </span>
                    <span class="wrapperPad">
                        <span class="title"><?= $good->name ?></span>
                        <span class="pricePosition">
                            <span class="text">Цена за 1 <?= ($good->measure_price == 1) ? 'шт' : 'кг' ?>.</span>
                            <span class="price">
                                <span class="new"><?= number_format($good->real_price(), 0, '', ' ') ?> Т</span>
                                <? if ($good->old_price): ?>
                                    <div class="old"><?= $good->old_price ?></div>
                                <? endif ?>
                            </span>
                            <? if ($good->measure_price != $good->measure && $good->weight != 1): ?>
                                <?
                                $weight = $good->weight;
                                $weight_str = ' кг';
                                if (is_float($weight) && $weight < 1) {
                                    $weight = $weight * 1000;
                                    $weight_str = ' гр.';
                                }
                                ?>
                                <span class="clear"></span>
                                <br>
                                <span class="text">Цена за <?= $weight . $weight_str ?></span>
                                <span class="price" style="height: auto !important;">
                                    <span class="new"><?= number_format($good->sum_price(1), 0, '', ' ') ?> Т</span>
                                    <? if ($good->old_price): ?>
                                        <span class="old"><?= $good->sum_price(1, 'main', $good->old_price) ?></span>
                                    <? endif ?>
                                </span>
                            <? endif ?>
                        </span>
                        <? if ($good->count($context->city)): ?>
                            <span class="wrapper_dB">
                                <span class="dynamicBlock">
                                    <span class="btn_addToCart addCart" data-id="<?= $good->id ?>" data-count="1">В корзину</span>
                                    <!--<span class="btn_buyToClick fastCart" data-id="<?= $good->id ?>">Купить в 1 клик</span>-->
                                </span>
                            </span>
                        <? else: ?>
                            <span class="wrapperNone">
                                <span class="None">Временно нет в наличии</span>
                            </span>
                        <? endif; ?>
                    </span>
                </a>
            <? endforeach; ?>
        </div>
    </div>
<? endif; ?>
<?php
$this->registerJs(<<<JS

$('.owl-carousel-0').owlCarousel({
  loop:true,
  lazyLoad: true,
  margin:4,
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
          slideBy: 2
      },
      1500: {
          items: 3,
          slideBy: 2
      }
  },
  nav:true,
  dots: false,
  navText: ['', '']
});


if ($(window).width() > 1000) {

    if ($('.owl-carousel-0 .owl-stage .owl-item').length < 7) {
        $('.owl-carousel-0').find('.owl-controls').css('display', 'none');
    } else {
        $('.owl-carousel-0').find('.owl-controls').css('display', '');
    }

} else {
$('.owl-carousel-0').find('.owl-controls').css('display', '');
}


$(window).resize(function() {
if ($(this).width() > 1000) {

    if ($('.owl-carousel-0 .owl-stage .owl-item').length < 7) {
        $('.owl-carousel-0').find('.owl-controls').css('display', 'none');
    } else {
        $('.owl-carousel-0').find('.owl-controls').css('display', '');
    }

} else {
        $('.owl-carousel-0').find('.owl-controls').css('display', '');
    }
});

JS
)
?>