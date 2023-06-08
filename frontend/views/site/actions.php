<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $items \common\models\Actions[]
 */
use common\components\Debugger as d;
use shadow\widgets\SLinkPager;
use yii\helpers\Url;

$context = $this->context;
//d::pri($context);
?>
<div class="Article articlelist">
    <h1 class="title padSpace">Акции</h1>
    <div class="articleBlocks bgWave padSpace">
        <? foreach ($items as $item): ?>
            <a href="<?= Url::to(['site/actions', 'id' => $item->id]) ?>" class="articleBlock">
                 <span class="image" style="background-image: url(<?= $item->img(true,'mini_list') ?>);">
                    <span class="stickerPosition">
                        <span class="popular"><?=$item->rang_date()?></span>
                    </span>
                </span>
                    <span class="wrapperText">
                        <span class="date"><?= date('d.m.Y', $item->created_at) ?></span>
                        <span class="title"><span><?= $item->name ?></span></span>
                        <? if ($item->small_body): ?>
                            <span class="desc"><span><?= $item->small_body ?></span></span>
                        <? endif ?>
                    </span>
            </a>
        <? endforeach; ?>
        <div class="clear"></div>
        <?
        /**
         * @var $pages yii\data\Pagination
         */
        echo SLinkPager::widget([
            'pagination' => $pages,
            'activePageCssClass' => 'current',
            'prevPageLabel' => false,
            'nextPageLabel' => false,
            'options' => [
                'class' => 'navigationBlock'
            ]
        ]);
        ?>
    </div>
</div>
