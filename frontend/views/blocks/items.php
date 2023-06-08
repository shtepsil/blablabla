<?php
/**
 * @var $this yii\web\View
 * @var $items \common\models\Items[]
 * @var $context \frontend\controllers\SiteController
 */
$context = $this->context;
use yii\helpers\Html;
use yii\helpers\Url;

$no_mark = !($context->id == 'site' && $context->action->id == 'index');
?>
<?php foreach ($items as $item): ?>
    <?php
    $class = '';
    if (!$item->count($context->city)) {
        $class .= ' isNone';
    }
    ?>
    <div class="goodsBlock<?= $class ?>">
        <a class="image" href="<?= $item->url() ?>" style="background-image: url(<?= $item->img(true,'item_320') ?>);">
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
        </a>
        <span class="wrapperPad">
            <a class="title" href="<?= $item->url() ?>"><span><?= $item->name ?></span></a>
            <span class="descript">
                <span><?= $item->body_small ?></span>
            </span>
            <span class="pricePosition">
                <span class="text">Цена за 1 <?= ($item->measure_price == 1) ? 'шт' : 'кг' ?>.</span>
                <span class="price">
                    <span class="new"><?= number_format($item->real_price(), 0, '', ' ') ?> Т</span>
                    <? if ($item->old_price): ?>
                        <span class="old"><?= number_format($item->old_price, 0, '', ' ') ?></span>
                    <? endif ?>
                </span>
            </span>
            <? if ($item->count($context->city)): ?>
                <span class="dynamicBlock">
                    <span class="btn_addToCart addCart" data-id="<?= $item->id ?>" data-count="1">
                        <?= (isset($context->cart_items[$item->id]) ? 'В корзине' : 'В корзину') ?>
                    </span>
                    <!--<span class="btn_buyToClick fastCart" data-id="<?= $item->id ?>">Купить в 1 клик</span>-->
                </span>
            <? else: ?>
                <span class="wrapperNone">
                    <span class="None">Временно нет в наличии</span>
                </span>
            <? endif; ?>
        </span>
    </div>
<?php endforeach; ?>
