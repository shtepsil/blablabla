<?php
/**
 * @var $item \common\models\Items | \common\models\OrdersItems
 * @var $select_types \common\models\ItemsTypeHandling[]
 * @var string $name
 */
use common\models\OrdersItems;
use yii\helpers\Html;

$discount = 0;
/**
 * @var $functions \frontend\components\FunctionComponent
 */
$functions = Yii::$app->function_system;
$order_item = $item;
$count = (double)$item->count;
$price = $item->price;
$weight = (double)$order_item->weight;
$purch_price = $item->purch_price;
$select_types = $item->getOrdersItemsHandings()->with('typeHandling')->indexBy('type_handling_id')->all();
$item = $item->item;
if ($item->purch_price != $purch_price) {
    $purch_price = $item->purch_price;
}
$item_price = $item->sum_price($count, 'main', 0, $weight);
if (isset($discounts)) {
    $item_price_discount = $functions->full_item_price($discounts, $item, $count, $weight);
    $discount = ($item_price - $item_price_discount);
    $item_price = $item_price - $discount;
}
?>
<tr>
    <td><?=$item->article?></td>
    <td>
        <?= $item->name ?>
    </td>
    <td style="text-align: center">
        <? if ($item->measure != $item->measure_price): ?>
            <?= doubleval($weight) ?>
        <? else: ?>
            <?= doubleval($count) ?>
        <? endif; ?>
    </td>
    <td style="text-align: center"><?= ($item->measure_price == 0) ? 'кг' : 'шт' ?></td>
    <td style="text-align: center"><?= number_format($price, 0, '.', ' ') ?></td>
    <td style="text-align: center"><?= ($discount) ? $discount : '' ?></td>
    <td style="text-align: right"><?= number_format($item_price, 0, '.', ' ') ?></td>
</tr>
