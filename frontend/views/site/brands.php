<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $items \common\models\Actions[]
 */
use shadow\widgets\SLinkPager;
use yii\helpers\Url;
   
$context = $this->context;
?>
<div class="Article articlelist">
    <h1 class="title padSpace">Бренды</h1>
    <div class="articleBlocks bgWave padSpace">
        <? foreach ($brands as $brand): ?>
            <a href="<?= Url::to(['site/brands', 'id' => $brand->id]) ?>" class="articleBlock">
                <span class="image" style="background-image: url(<?php echo $brand->img; //echo $brand->img(true,'mini_list') ?>);">
                    <span class="stickerPosition">
                  
                    </span>
                </span>
                    <span class="wrapperText">

                        <span class="title"><span><?= $brand->name ?></span></span>
                       <!-- <? //if ($item->small_body): ?>
                            <span class="desc"><span><?php // $item->small_body ?></span></span>
                        <? //endif ?>-->
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
