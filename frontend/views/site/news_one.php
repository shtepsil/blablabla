<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $item common\models\News
 * @var $news common\models\News[]
 */
use yii\helpers\Html;
use yii\helpers\Url;

$context = $this->context;
?>

<div class="breadcrumbsWrapper padSpace">
    <?= $this->render('//blocks/breadcrumbs') ?>
</div>
<div class="Article articleInner padSpace">
    <div class="title"><?=$item->name?></div>
    <div class="articleBlock">
        <div class="image" style="background-image: url(<?=$item->img?>);margin-right: 25px;margin-bottom: 25px;">
        </div>
        <div class="wrapperText">
            <div class="date"><?=date('d.m.Y',$item->created_at)?></div>
            <style>
                .Article.articleInner .articleBlock .wrapperText .desc p {
                    margin-bottom: 20px;
                }
            </style>
            <div class="desc" style="color: #343332;line-height: 1.5em;">
                <?=$item->body?>
            </div>
        </div>
    </div>
</div>
<? if ($news): ?>
    <div class="Article articlePosition bgWave padSpace">
        <div class="bTitle">Еще отличные новости</div>
        <div class="articleBlocks">
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
        </div>
    </div>
<? endif ?>