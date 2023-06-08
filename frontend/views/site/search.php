<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $news \common\models\News[]
 * @var $items \common\models\Items[]
 */
use yii\helpers\Url;

$context = $this->context;
?>
    <div class="Goods goodslist padSpace sPage">
        <h1 class="title">Результаты поиска</h1>
        <form action="<?= Url::to(['site/search']) ?>" method="get" class="SearchPage">
            <input type="text" value="<?= $query ?>" name="query">
            <button class="custom" type="submit"></button>
        </form>
        <br><br><br>
        <? if ($items): ?>
            <div class="searchResTitle"><?= Yii::t('main', 'count_items', ['n' => count($items)]) ?>:</div>
            <div class="goodsBlocks">
                <?= $this->render('//blocks/items', ['items' => $items]) ?>
            </div>
        <? endif ?>
    </div>
<? if ($news): ?>
    <div class="Article newlist">
        <div class="articleBlocks bgWave padSpace">
            <div class="searchResTitle"><?= Yii::t('main', 'count_search_news', ['n' => count($news)]) ?>:</div>
            <? foreach ($news as $item): ?>
                <a href="<?= Url::to(['site/news', 'id' => $item->id]) ?>" class="articleBlock">
                    <span class="image" style="background-image: url(<?= $item->img ?>);"></span>
                    <span class="wrapperText">
                        <span class="date"><?= date('d.m.Y', $item->created_at) ?></span>
                        <span class="title"><?= $item->name ?></span>
                        <? if ($item->small_body): ?>
                            <span class="desc"><?= $item->small_body ?></span>
                        <? endif ?>
                    </span>
                </a>
            <? endforeach; ?>
            <div class="clear"></div>
        </div>
    </div>
<? endif ?>