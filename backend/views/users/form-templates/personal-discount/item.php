<?php
/**
 * Шаблон строки товара для добавления в настройки пользователя,
 * для добавления персональной скидки для оптовика на добавленный товар.
 * @var $item \common\models\Items | \common\models\OrdersItems
 * @var $type_handling \common\models\ItemsTypeHandling[]
 * @var string $form_name
 */
use common\components\Debugger as d;
use common\models\Items;
use common\models\User;
use yii\helpers\Html;
/**
 * @var $functions \frontend\components\FunctionComponent
 */
$functions = Yii::$app->function_system;
$discount = (isset($discount)) ? $discount : 0;

$order_item = $item;
$count = (double)$item->count;
/*
 * В EditUser->FormParams User::$user_type задаётся в самом начале
 * но здесь ещё есть добавление нового товара для скидки,
 * по этому User::$user_type задаётся здесь ещё раз.
 */
if(isset($isWholesale)) User::$user_type = $isWholesale;
if(isset($user_id)) User::$id = $user_id;

/*
 * Зададим оптовую цену, и аргументом false
 * выключим применение персональной скидки...
 * Нужно чтобы $item->price просто была оптовой, без скидки.
 * Это нужно чтобы в поле "Цена за ед." была выведена
 * просто оптовая цена, без персональной оптовой скидки.
 */
$weight = (double)$order_item->weight;

$item->setPriceWholesale();
$price = $item->price;

if($weight != 0){
    /*
     * Если поле "Скидка" сделать 0 или пустым,
     * то применится вот эта переменная $item_price_orig
     */
    $item_price_orig = $item->price * $weight;
}else{
    $item_price_orig = $item->price;
}

/*
 * А персональную скидку применим отдельно,
 * чтобы в поле "Стоимость" была выведена сумма с применённой скидкой.
 */
$item->personalDiscountWholesale();

$purch_price = $item->purch_price;
if ($item->purch_price != $purch_price) {
    $purch_price = $item->purch_price;
}
$item_price = $item->sum_price(1, 'main', $item->price, $weight);
//d::pri($item_price);

//number_format($item->sum_price($count = 1, $type = 'main', $price = 0, $weight = 0), 0, '', ' ');

$input_name = "EditUser[personal_discount][item_{$item->id}]";

$tg = '';
if(preg_match('/%$/i', $discount)){
    $tg = 'hide';
}

if($discount){
    if(preg_match('/%$/', $discount)){
        $item_price = $functions->percentageCalculation($item_price, $discount);
    } else {
//        $item_price -= $discount;
    }
}

?>
<tr class="item" id="<?= $form_name ?>_<?= $item->id ?>">
    <td>
        <?= Html::a($item->name, ['items/control', 'id' => $item->id], ['target' => '_blank']) ?>
    </td>
    <td class="item_price"><?= $price ?></td>
    <td><?= ($item->measure_price == 0) ? 'кг' : 'шт' ?></td>
    <td class="item_weight"><?
        if ($item->measure != $item->measure_price){
            echo $weight;
        }
        ?></td>
    <td class="sum_item_discount">
        <div class="wrap-discount">
            <?= Html::textInput($input_name, $discount, [
                'class' => 'form-control',
            ]) ?><span class="<?=$tg?>">Тг</span>
        </div>
        <?//= $item_price_orig?>
    </td>
    <td class="sum_item" data-weigth-origin="<?=$item_price_orig?>" data-origin="<?= $price ?>" data-weight="<?=($weight AND $weight != 1) ? $weight : 1?>" style="text-align: center;"><?= floor($item_price) ?></td>
    <td class="actions text-center deleted-<?= $form_name ?>">
        <a href="#" class="btn btn-xs btn-danger" title="Удалить" data-id="<?= $item->id ?>"><i class="fa fa-times fa-inverse"></i></a>
    </td>
</tr>
