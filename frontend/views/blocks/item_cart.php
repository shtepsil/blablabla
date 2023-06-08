<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 *
 *
 */
use common\models\Items;
use yii\helpers\Url;

$context = $this->context;
?>

<? if ($item instanceof Items): ?>
    <?
    /**
     * @var $item \common\models\Items
     */
    $string_measure = '{count}';
    $type_handling = Yii::$app->session->get('type_handling', []);
    if ($item->measure != $item->measure_price) {
        if ($item->measure_price == 0) {
            $string_measure = $item->weight . ' кг x {count} шт.';
        } else {
            $string_measure = '{count} кг / ' . $item->weight . ' кг';
        }
    } else {
        $string_measure = '{count} ' . (($item->measure == 1) ? 'шт.' : 'кг');
    }
    ?>
    <div class="cartBlock" data-item_id="<?= $item->id ?>">
        <div class="delGoods" data-id="<?= $item->id ?>" data-type="item"></div>
        <div class="image" style="background-image: url(<?= $item->img() ?>);"></div>
        <div class="description">
            <div class="name"><?= $item->name ?></div>
            <? if ($item->body_small): ?>
                <div class="minidesc"><?= $item->body_small ?></div>
            <? endif ?>
            <? if ($item->article): ?>
                <div class="article">Артикул: <?= $item->article ?></div>
            <? endif ?>
            <div class="num" data-val="<?= $string_measure ?>"><?= str_replace('{count}', $count, $string_measure) ?></div>
            <div class="price"><?= number_format($item->sum_price($count), 0, '', ' ') ?> Т</div>
        </div>
        <div class="clear"></div>
        <? if ($item->itemsTypeHandlings): ?>
            <div class="switchType_panel">
                <?php foreach ($item->itemsTypeHandlings as $item_handling): ?>
                    <?php
                    $checked = false;
                    if (!$item_handling->typeHandling->isVisible) {
                        continue;
                    }
                    $checked = (isset($type_handling[$item->id]) && in_array($item_handling->typeHandling->id, $type_handling[$item->id]));
                    ?>
                    <div class="string">
                        <input type="radio" value="<?= $item_handling->typeHandling->id ?>" <?= ($checked) ? 'checked' : '' ?>
                               id="item_cart_handling_<?= $item_handling->id ?>" name="type_handling_<?=$item->id?>[]"
                               class="radio_cart_handling"
                               data-id="<?= $item->id ?>">
                        <label for="item_cart_handling_<?= $item_handling->id ?>"><?= $item_handling->typeHandling->name ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        <? endif ?>
    </div>
<? else: ?>
    <?
    /**
     * @var $item \common\models\Sets
     */
    ?>
    <div class="cartBlock">
        <div class="delGoods" data-id="<?= $item->id ?>" data-type="set"></div>
        <div class="image" style="background-image: url(<?= $item->img ?>);"></div>
        <div class="description">
            <div class="name"><?= $item->name ?></div>
            <div class="num" data-val="{count} шт."><?= $count ?> шт.</div>
            <div class="price"><?= number_format(round($item->real_price() * $count), 0, '', ' ') ?> Т</div>
        </div>
    </div>
<? endif; ?>
