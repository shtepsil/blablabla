<?php

use common\components\Debugger as d;
use common\components\helpers\CHtml;

/**
 * @var $reviews \common\models\Reviews[]
 */
$context = $this->context;

?>
<? 
//echo count($reviews);  
foreach ($reviews as $review): ?>

    <div class="reviewBlock">
        <div class="revAvatar" style="background-image: url(<?= $context->AppAsset->baseUrl ?>/images/avatar_reviews.png);"></div>
        <div <?=$md->get('review','itemscope')?> class="revDesc">
            <div <?=$md->get('person','itemscope')?> class="revLine">
                <?=$md->getMetaProp('name',$review->name)?>
                <div class="name"><b><?= $review->name ?></b></div>

                <div class="date"><?= Yii::$app->formatter->asDate($review->created_at, 'd MMMM Y HH:mm'); ?></div>
                <div <?=$md->get('rating','itemscope')?> class="Rating">
                    <?=$md->getMetaProp('ratingValue',$review->rate)?>
                    <?=$md->getMetaProp('bestRating','5')?>
                    <div class="star <?= ($review->rate >= 1) ? 'check' : '' ?>">
                        <div class="star <?= ($review->rate >= 2) ? 'check' : '' ?>">
                            <div class="star <?= ($review->rate >= 3) ? 'check' : '' ?>">
                                <div class="star <?= ($review->rate >= 4) ? 'check' : '' ?>">
                                    <div class="star <?= ($review->rate >= 5) ? 'check' : '' ?>"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="revLine text">
                <p><?= $review->body ?></p>
            </div>
        </div>
    </div>
<? endforeach; ?>