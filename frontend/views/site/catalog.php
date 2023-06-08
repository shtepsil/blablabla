<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $items array
 * @var $order string
 * @var $measure string
 * @var $items_cat \common\models\Items[]
 * @var $model \common\models\Items
 * @var $cat \common\models\Category
 * @var $cats \common\models\Category[]
 * @var $sub_cats \common\models\Category[]
 * @var $sub_cat \common\models\Category
 *
 */
use common\components\Debugger as d;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
//d::pri($cat);

$context = $this->context;

$cat_title = $cat->title;
$cat_body = $cat->body;

if($sub_cat){
    $cat_title = $sub_cat->title;
    $cat_body = $sub_cat->body;
}

?>
<div class="Goods goodslist padSpace">
    <h1 class="title"><?= ($sub_cat ? $sub_cat->name : $cat->name) ?></h1>
    <div class="Filter line">
        <? if ($cats): ?>
            <ul class="list">
                <?php
                $li_cats = '';
                $select_cat = false;
                foreach ($cats as $sub) {
                    if ($sub->id == $cat->id) {
                        $content_li = $sub->name;
                        $select_cat = $sub->parent;
                    } else {
                        $content_li = Html::a($sub->name, $sub->url());
                    }
                    $li_cats .= Html::tag('li', $content_li);
                }
                ?>
                <?php if ($select_cat): ?>
                    <li><?= Html::a('Все', $select_cat->url()) ?></li>
                <?php else: ?>
                    <li>Все</li>
                <?php endif; ?>
                <?= $li_cats ?>
            </ul>
        <? endif ?>
        <ul class="sort">
            <li>Сортировать по</li>
            <?
            $sort_li = '';
            if ($order == 'price_asc') {
                $sort_li .= Html::tag(
                    'li',
                    Html::a(
                        'Названию',
                        $cat->url(['order' => 'name_asc'])
                    )
                );
                $sort_li .= Html::tag(
                    'li',
                    'Цене',
                    [
                        'class' => 'byName'
                    ]
                );
                $sort_li .= Html::tag(
                    'li',
                    Html::a(
                        'Популярности',
                        $cat->url(['order' => 'popularity_desc'])
                    )
                );
            } elseif ($order == 'name_asc') {
                $sort_li .= Html::tag(
                    'li',
                    'Названию',
                    [
                        'class' => 'byName'
                    ]
                );
                $sort_li .= Html::tag(
                    'li',
                    Html::a(
                        'Цене',
                        $cat->url(['order' => 'price_asc'])
                    )
                );
                $sort_li .= Html::tag(
                    'li',
                    Html::a(
                        'Популярности',
                        $cat->url(['order' => 'popularity_desc'])
                    )
                );
            }elseif($order == 'popularity_desc'){
                $sort_li .= Html::tag(
                    'li',
                    Html::a(
                        'Названию',
                        $cat->url(['order' => 'name_asc'])
                    )
                );
                $sort_li .= Html::tag(
                    'li',
                    Html::a(
                        'Цене',
                        $cat->url(['order' => 'price_asc'])
                    )
                );
                $sort_li .= Html::tag(
                    'li',
                    'Популярности',
                    [
                        'class' => 'byName'
                    ]
                );
            }
            echo $sort_li
            ?>
        </ul>
        <? if ($sub_cats): ?>
            <?php
            $li_cats = '';
            foreach ($sub_cats as $sub) {
                $options_sub = [];
                if ($sub_cat && $sub->id == $sub_cat->id) {
                    $options_sub['class'] = 'current';
                }
                $li_cats .= Html::tag('li', Html::a($sub->name, $sub->url()), $options_sub);
            }
            ?>
            <ul class="manyTags">
                <?= $li_cats ?>
            </ul>
        <? endif ?>
    </div>
    <!--    <div class="goodsBlocks" data-check="height">-->
    <div class="goodsBlocks">
        <?= $this->render('//blocks/items', ['items' => $items]) ?>
    </div>

    <?if($cat_title AND $cat_body):?>
        <!-- Category description -->
        <div class="cat-description">
            <div class="c-title h4"><?=$cat_title?></div>
            <div class="c-text"><?=$cat_body?></div>
        </div>
        <!-- /category description -->
    <?endif?>
</div>