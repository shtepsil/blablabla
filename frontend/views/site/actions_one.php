<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $item common\models\Actions
 * @var $actions common\models\Actions[]
 */
use yii\helpers\Html;
use yii\helpers\Url;

$context = $this->context;
?>
    <div class="breadcrumbsWrapper padSpace">
        <?= $this->render('//blocks/breadcrumbs') ?>
    </div>
    <div class="Article articleInner padSpace">
        <div class="title"><?= $item->name ?></div>
        <div class="articleBlock">
            <div class="image" style="background-image: url(<?= $item->img ?>);">
                <div class="stickerPosition">
                    <div class="popular"><?= $item->rang_date() ?></div>
                </div>
            </div>
            <div class="wrapperText">
                <div class="date"><?= date('d.m.Y', $item->created_at) ?></div>
                <div class="desc">
                    <?= $item->body ?>
                </div>
            </div>
        </div>
    </div>
<? if ($items_action=$item->getItems()->andWhere(['isVisible'=>1])->all()): ?>
    <div class="Goods goodslist padSpace">
        <div class="mTitle">Товары по акции:</div>
        <div class="goodsBlocks" data-check="height">
            <?=$this->render('//blocks/items',['items'=>$items_action])?>
        </div>
    </div>
<? endif; ?>
<? if ($actions): ?>
    <div class="Article articlePosition bgWave padSpace">
        <div class="bTitle">Другие акции</div>
        <div class="articleBlocks">
            <? foreach ($actions as $item): ?>
                <a href="<?= Url::to(['site/actions', 'id' => $item->id]) ?>" class="articleBlock">
                    <span class="image" style="background-image: url(<?= $item->img ?>);">
                        <span class="stickerPosition">
                        <span class="popular"><?=$item->rang_date()?></span>
                    </span>
                    </span>
                    <span class="wrapperText">
                        <span class="date"><?= date('d.m.Y', $item->created_at) ?></span>
                        <span class="title"><?= $item->name ?></span>
                        <? if ($item->small_body): ?>
                            <span class="desc"><?= $item->small_body ?></span>
                        <? endif ?>
                    </span>
                </a>
            <? endforeach; ?>
        </div>
    </div>
<? endif ?>