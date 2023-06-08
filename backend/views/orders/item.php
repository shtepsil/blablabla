<?php
/**
 * @var $item \common\models\Items | \common\models\OrdersItems
 * @var $type_handling \common\models\ItemsTypeHandling[]
 * @var string $name
 */
use common\components\Debugger as d;
use common\models\OrdersItems;
use common\models\User;
use yii\helpers\Html;

$discount = 0;
/**
 * @var $functions \frontend\components\FunctionComponent
 */
$functions = Yii::$app->function_system;
if ($item instanceof OrdersItems) {
    /*
     * Этот блок запускается для вывода строк товара заказа
     * в заказе в админке
     */
    $order_item = $item;
    $count = (double) $item->count;
    $price = $item->price;
    $weight = (double) $order_item->weight;
    $purch_price = $item->purch_price;
    $select_types = $item->getOrdersItemsHandings()->indexBy('type_handling_id')->all();
    $item = $item->item;
    if ($item->purch_price != $purch_price) {
        $purch_price = $item->purch_price;
    }
    $type_handling = $item->getItemsTypeHandlings()->indexBy('type_handling_id')->with('typeHandling')->all();
    $item_price = $item->sum_price($count, 'main', $price, $weight);
    if (isset($discounts) && $discounts) {
        $item_price_discount = $functions->full_item_price($discounts, $item, $count, $weight);
        $discount = ($item_price - $item_price_discount);
        $item_price = $item_price - $discount;
    }
} else {
    // Этот блок запускается при добавлении нового товара в заказ в админке
    $select_types = [];
    $weight = $item->weight;
    $count = 1;
    /*
     * Настроим объект User, чтобы перед получением цены товара,
     * учесть все настройки пользователя для цены.
     * Если не настроить пользователя, то оптовые цены не будут учитываться,
     * а будут цены для обычного пользователя.
     */
    if(isset($isWholesale)) User::$user_type = $isWholesale;
    if(isset($user_id)) User::$id = $user_id;
    $price = $item->real_price();
    $purch_price = $item->purch_price;
    $type_handling = $item->getItemsTypeHandlings()->indexBy('type_handling_id')->with('typeHandling')->all();
    $item_price = $item->sum_price($count, 'main', $price);
}
//$discount_item = isset($user->personal_discount[$item->id]) ? $user->personal_discount[$item->id] : 0;
//if($discount_item > 0 AND !preg_match('/%$/', $discount_item)){
//    $discount_item .= 'Тг';
//}
$input_name = $name . "[{$item->id}]";

?>
<tr class="item" id="<?= $name ?>_<?= $item->id ?>">
    <td>
        <?= Html::a($item->name, ['items/control', 'id' => $item->id], ['target' => '_blank']) ?>
        <?= Html::hiddenInput("{$input_name}[purch_price]", $purch_price, [
            'data-purch_price' => $price
        ]) ?>
        <div class="form-inline">
            <?php foreach ($type_handling as $value): ?>
                <div class="input-group">
                    <div class="checkbox">
                        <?= Html::checkbox("{$input_name}[type_handling][]", isset($select_types[$value->type_handling_id]), [
                            'label' => $value->typeHandling->name,
                            'value' => $value->type_handling_id
                        ]) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </td>
    <td class="item_price">
        <?
//        if($isWholesale > 0 AND isset($user->personal_discount[$item->id])){
//            $price = $item->old_price;
//        }
        if (Yii::$app->user->can('change_price_item_order')) {
            echo Html::textInput("{$input_name}[price]", $price, [
                'class' => 'form-control price_item',
            ]);
        } else {
            echo $price;
        }
        ?>
    </td>
    <td>
        <?=($item->measure_price == 0) ? 'кг' : 'шт' ?>
    </td>
    <td>
        <div class="form-inline ">
            <div class="form-group ">
                <?= Html::textInput("{$input_name}[count]", doubleval($count), [
                    'class' => 'form-control',
                    'style' => 'width: 50px;',
                ]) ?>
                <label>
                    <?=($item->measure == 0) ? 'кг' : 'шт' ?>.
                </label>
            </div>
        </div>
    </td>
    <td>
        <? if ($item->measure != $item->measure_price): ?>
            <?= Html::textInput("{$input_name}[weight]", doubleval($weight), [
                'class' => 'form-control',
            ]) ?>
        <? endif; ?>
    </td>
    <td class="sum_item_discount">
        <?=$discount?>
    </td>
    <td class="sum_item">
        <?= $item_price ?>
    </td>
    <td class="actions text-center deleted-<?= $name ?>">
        <a href="#" class="btn btn-xs btn-danger" title="Удалить" data-id="<?= $item->id ?>">
            <i class="fa fa-times fa-inverse"></i></a>
    </td>
</tr>