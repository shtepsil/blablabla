<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $item \common\models\Sets
 *
 */
$context = $this->context;
?>
<div class="breadcrumbsWrapper padSpace">
    <?= $this->render('//blocks/breadcrumbs') ?>
</div>
<div class="Article articleInner padSpace">
    <div class="title"><?= $item->name ?></div>
    <div class="articleBlock">
        <div class="image" style="background-image: url(<?= $item->img ?>);">
        </div>
        <div class="wrapperText">
            <div class="desc">
                <div>
                    <?= $item->body ?>
                </div>
            </div>
            <div class="goodsBlock">
                <span class="wrapperPad">
                    <span class="pricePosition">
                        <span class="price">
                            <span class="new"><?=number_format($item->real_price(), 0, '', ' ')?> т.</span>
                            <span class="eco">Экономия <?=number_format($item->saving_price(), 0, '', ' ')?> т.</span>
                        </span>
                    </span>
                    <span class="dynamicBlock">
                        <span class="btn_addToCart addSets" data-id="<?=$item->id?>">В корзину</span>
                        <span class="btn_buyToClick fastSets" data-id="<?=$item->id?>">Купить в 1 клик</span>
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>



