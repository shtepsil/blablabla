<?php

/**
 * @var $context \frontend\controllers\SiteController
 * @var $this \yii\web\View
 * @var $page \backend\models\Pages
 * @var $history_all \common\models\AboutHistory[]
 */
$context = $this->context;

?>
<div class="About">
    <div class="Us padSpace" style="background-image: url(<?= $context->AppAsset->baseUrl ?>/images/bg_fish.jpg);">
        <div class="title"><?=$page->name?></div>
        <div class="text">
           <?=$page->body?>
        </div>
    </div>
    <? if ($history_all): ?>
        <div class="History padSpace">
            <div class="title">История компании</div>
            <div class="text">
                <p><b><?=$context->settings->get('text_header_about_history')?></b></p>
                <ul class="dateList">
                    <? foreach($history_all as $history): ?>
                        <li>
                            <p class="date"><b><?=$history->year?></b></p>
                            <p class="desc"><?=$history->body?></p>
                        </li>
                    <? endforeach; ?>
                </ul>
            </div>
        </div>
    <? endif ?>
</div>
