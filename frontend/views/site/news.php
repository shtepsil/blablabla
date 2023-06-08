<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $items \common\models\News[]
 * @var $years array
 * @var $main_year integer
 * @var $select_year integer
 */
use common\models\News;
use shadow\widgets\SLinkPager;
use yii\helpers\Html;
use yii\helpers\Url;

$context = $this->context;
$data_select = [];
?>
<div class="Article newlist">
    <div class="titleLine padSpace">
        <h1 class="title">Новости</h1>

        <div class="Filter line">
            <ul class="list">
                <? foreach ($years as $year): ?>
                    <? $data_select[$year] = $year; ?>
                    <? if ($year == $main_year): ?>
                        <? if(!$select_year): ?>
                            <? $select_year=$year?>
                            <li><?= $year ?></li>
                        <? else: ?>
                            <li>
                                <a href="<?= Url::to(['site/news']) ?>" id="select_<?=$year?>"><?= $year ?></a>
                            </li>
                        <? endif; ?>
                    <? elseif ($select_year == $year): ?>
                        <li><?= $year ?></li>
                    <? else: ?>
                        <li>
                            <a href="<?= Url::to(['site/news', 'year' => $year]) ?>" id="select_<?=$year?>"><?= $year ?></a>
                        </li>
                    <? endif; ?>
                <? endforeach; ?>
            </ul>
            <div class="list_mobile">
                <span class="val"><?=$select_year?></span>
                <?=Html::dropDownList('change_year',$select_year,$data_select,['id'=>'blablabla'])?>
                <?
                $this->registerJs(<<<JS
$('#blablabla').on('change', function () {
    var val = $(this).val();
    var href = $('#select_' + val).attr('href');
    window.location.href = href;
});
JS
)
                ?>
            </div>
        </div>
    </div>
    <div class="articleBlocks bgWave padSpace" data-check="height">
        <? foreach ($items as $item): ?>
            <a href="<?= Url::to(['site/news', 'id' => $item->id]) ?>" class="articleBlock">
                <span class="image" style="background-image: url(<?= $item->img(true,'mini_list') ?>);"></span>
                    <span class="wrapperText">
                        <span class="date"><?= date('d.m.Y', $item->created_at) ?></span>
                        <span class="title"><?= $item->name ?></span>
                        <? if ($item->small_body): ?>
                            <span class="desc"><?= $item->small_body ?></span>
                        <? endif ?>
                    </span>
            </a>
        <? endforeach; ?>
<!--        <div class="clear"></div>-->
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
