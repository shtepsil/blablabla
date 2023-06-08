<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $items \common\models\Recipes[] Рецепты
 * @var $pages Pagination
 * @var $hasPage
 *
 */
use yii\data\Pagination;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$context = $this->context;
?>
<div class="Article articlelist">
    <h1 class="title padSpace">Рецепты</h1>

    <div class="articleBlocks bgWave padSpace" data-check="height">
        <?= $this->render('//blocks/recipe', ['items' => $items]) ?>
<!--        <div class="clear"></div>-->
    </div>
    <? if ($hasPage): ?>
        <div class="linkMore" id="next_page">Еще рецепты</div>
    <? endif ?>
    <div class="socialFacebook">
        <div class="fb-follow" data-href="https://www.facebook.com/zuck" data-layout="standard" data-show-faces="true"></div>
    </div>
</div>
<?php
if ($hasPage) {
    $url = Url::to(['site/recipes']);
    $this->registerJs(<<<JS
var page = 0;
$('#next_page').on('click', function () {
    page++;
    $.ajax({
        url: '',
        method: 'GET',
        data: {page: page},
        success: function (data) {
            if (typeof data.items != 'undefined') {
                $('.articleBlock:last', '.articleBlocks').after(data.items)
            }
            if (!data.hasPage) {
                $('#next_page').hide();
            }
        }
    })
});

JS
    );
}
?>
