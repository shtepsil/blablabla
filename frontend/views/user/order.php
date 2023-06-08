<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\UserController
 * @var $order Orders
 * @var $old_orders Orders[]
 */
use common\components\Debugger as d;
use common\models\Orders;
use frontend\assets\WidgetCloudpaymentsAsset;
use frontend\form\ReplayOrder;
use frontend\widgets\ActiveField;
use frontend\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$context = $this->context;
$user = $context->user;

$invoice_params = [
    'class' => 'btn_Form blue send invoice-print',
    'disabled' => 'disabled',
    'style' => 'cursor: no-drop;background-color:#4686cc99;',
];
$invoice_status = ', (ещё не загружена)';

if ($order->invoice_file) {
    unset($invoice_params['disabled'], $invoice_params['style']);
    $invoice_status = ', Загружена';
}

WidgetCloudpaymentsAsset::register($this);

?>
<div class="breadcrumbsWrapper padSpace">
    <?= $this->render('//blocks/breadcrumbs') ?>
</div>
<div class="Cabinet padSpace">
    <div class="gTitle_line">
        <div class="gTitle">
            Заказ №
            <?= $order->id ?>
        </div>
        <div style="clear: both;"></div><br>
        <? if ($order->payment == 2 and $order->pay_status == 'wait'): ?>
            <div class="order-status">
                <!--                <span>Не оплачен</span>&nbsp;&nbsp;-->
                <button type="button" class="btn_Form blue go-pay-order" data-order-id="<?= $order->id ?>">
                    Оплатить заказ
                </button>
            </div>
        <? endif ?>
        <div class="right_line">
            <div class="delivered">
                Оформлен
                <?= Yii::$app->formatter->asDate($order->created_at, 'd MMMM Y'); ?> г.
                <? if ($order->isWholesale and $is_requisites): ?>
                    <div class="opt-btns">
                        <a href="<?= Url::to(['user/print-invoice-payment', 'id' => $order->id]) ?>" target="_blank">
                            <?= Html::button(
                                'Счёт для оплаты',
                                ['class' => 'btn_Form blue send invoice-print']
                            ) ?>
                        </a>
                        <a href="<?=($order->invoice_file) ?: '#' ?>" target="_blank">
                            <?= Html::button(
                                'Накладная' . $invoice_status,
                                $invoice_params
                            ) ?>
                        </a>
                    </div>
                <? endif ?>
            </div>
            <div class="dlc_links">
                <a href="<?= Url::to(['user/replay-order', 'id' => $order->id]) ?>"
                    class="reorder"><span>Перезаказать</span></a>
                <? if (false): ?>
                    <a href="#" class="print"><span>Распечатать</span></a>
                <? endif ?>
            </div>
        </div>
    </div>
    <table class="adpTable order">
        <thead>
            <tr>
                <td class="zN">№</td>
                <td class="zGoods">Товар</td>
                <td class="zNum">Количество</td>
                <td class="zPrice">Цена</td>
                <td class="zRes">Итог</td>
            </tr>
        </thead>
        <tbody>
            <?
            $sum = 0;
            $i = 0;
            ?>
            <?php foreach ($order->ordersItems as $item_order): ?>
                <?php
                $count = (double) $item_order->count;
                $type_handling = [];
                $type_handling[$item_order->item_id] = $item_order->getOrdersItemsHandings()->select('type_handling_id')->column();
                $no_item = false;
                $item = $item_order->item;
                $item_sum = $item->sum_price($count, 'main', $item_order->price, $item_order->weight);
                $price_item = number_format($item_order->price, 0, '', ' ');
                $sum += $item_sum;
                ?>
                <tr id="items-<?= $item->id ?>">
                    <td class="zN" data-title="№">
                        <?=++$i ?>
                    </td>
                    <td class="zGoods" data-title="Товар">
                        <a href="<?= $item->url() ?>"><?= $item->name ?></a>
                        <br />
                        <? if ($item->article): ?>
                            <span>арт.
                                <?= $item->article ?>
                            </span>
                        <? endif ?>
                    </td>
                    <td class="zNum" data-title="Количество">
                        <?= $count ?>     <?=($item->measure == 1) ? 'шт' : 'кг' ?>
                        <?
                        if ($item->measure_price != $item->measure && $item_order->weight && $item->measure_price == 0) {
                            echo '<br/> Вес: ';
                            echo ($item_order->weight . ' кг');
                        }
                        ?>
                        <? if ($item->itemsTypeHandlings): ?>
                            <?php
                            $handing_string = '';
                            foreach ($item->itemsTypeHandlings as $item_handling) {
                                if (!$item_handling->typeHandling->isVisible || !isset($handling[$item_handling->type_handling_id])) {
                                    continue;
                                }
                                if ($handing_string) {
                                    $handing_string .= '<br>';
                                }
                                $handing_string .= '+' . $item_handling->typeHandling->name;
                            }
                            if ($handing_string) {
                                echo Html::tag('span', $handing_string);
                            }
                            ?>
                        <? endif ?>
                    </td>
                    <td class="zPrice" data-title="Цена"><b>
                            <?= $price_item ?> т.
                        </b></td>
                    <td class="zRes" data-title="Итог">
                        <b>
                            <?= number_format($item_sum, 0, '', ' ') ?> т.
                        </b>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php foreach ($order->ordersSets as $order_set): ?>
                <?php
                $count = $order_set->count;
                $item = $order_set->set;
                $item_sum = round($count * $item->real_price());
                $sum += $item_sum;
                ?>
                <tr id="sets-<?= $item->id ?>">
                    <td class="zN" data-title="№">
                        <?=++$i ?>
                    </td>
                    <td class="zGoods" data-title="Товар"><a href="<?= Url::to(['site/sets']) ?>"><?= $item->name ?></a>
                        <br />
                    </td>
                    <td class="zNum" data-title="Количество">
                        <?= $count ?>
                    </td>
                    <td class="zPrice" data-title="Цена"><b>
                            <?= number_format($item->real_price(), 0, '', ' ') ?> т.
                        </b></td>
                    <td class="zRes" data-title="Итог">
                        <b>
                            <?= number_format($item_sum, 0, '', ' ') ?> т.
                        </b>
                    </td>
                </tr>
            <?php endforeach; ?>
            <? if ($order->price_delivery): ?>
                <tr class="result">
                    <td class="zN"></td>
                    <td class="zGoods" colspan="3">Доставка</td>
                    <td class="zRes"><b>
                            <?=($order->price_delivery) ? ($order->price_delivery . ' т.') : '' ?>
                        </b></td>
                </tr>
            <? endif ?>
            <? if ($order->bonus_use): ?>
                <tr class="result">
                    <td class="zN"></td>
                    <td class="zGoods" colspan="3">Использовано бонусов</td>
                    <td class="zRes"><b>
                            <?= number_format($order->bonus_use, 0, '', ' ') ?> т.
                        </b></td>
                </tr>
            <? endif ?>
            <? if (trim($order->discount)): ?>
                <tr class="result">
                    <td class="zN"></td>
                    <td class="zGoods" colspan="3">Скидка</td>
                    <td class="zRes"><b>
                            <?= $order->discount ?>
                        </b></td>
                </tr>
            <? endif ?>
            <?
            $sum_real = (($sum + $order->price_delivery) - $order->discount($sum)) - $order->bonus_use;
            ?>
            <tr class="result">
                <td class="zN"></td>
                <td class="zGoods" colspan="3">Итого к оплате</td>
                <td class="zRes"><b>
                        <?= number_format($sum_real, 0, '', ' ') ?> т.
                    </b></td>
            </tr>
            <? if ($order->bonus_add > 0 && $order->isWholesale == 0): ?>
                <tr class="result">
                    <td class="zN"></td>
                    <td class="zGoods" colspan="3">Начислено бонусов</td>
                    <td class="zRes"><b>
                            <?= $order->bonus_add ?>
                        </b></td>
                </tr>
            <? endif ?>
        </tbody>
    </table>
    <div class="additionalInformation">
        <div class="block_inf">
            <div class="title">Адрес доставки</div>
            <div class="text">
                <?= $order->user_name ?> <br />
                <?= $order->user_address ?> <br />
                T:
                <?= $order->user_phone ?> <br />
            </div>
        </div>
        <div class="block_inf">
            <div class="title">Метод оплаты</div>
            <div class="text">
                <?= $order->data_payment[$order->payment] ?>
            </div>
        </div>
    </div>
</div>
<?php

if ($order->payment == 2 and $order->pay_status == 'wait') {
    $publicId = Yii::$app->params['cloudpayments']['public_id'];
    $order_id = $order->id;
    $url = Url::to(['site/success-order']);
    $url_pay_fail = Url::to(['site/success-order', 'pay_fail' => '1']);
    $this->registerJs(<<<JS

$('.order-status button', '.Cabinet').on('click', function(){
    var orderStatus = $('.order-status', '.Cabinet');
    var widget = new cp.CloudPayments();
    widget.auth({ // options
        publicId : '{$publicId}', //id из личного кабинета
        description : 'Оплата заказа на сайте kingfisher.kz', //назначение
        amount : {$sum_real}, //сумма
        currency : 'KZT', //валюта
        invoiceId : '{$order->id}', //номер заказа  (необязательно)
        accountId : '{$order->user_id}', //идентификатор плательщика (необязательно)
        email:'{$order->user_mail}'
    },
    function (options) { // success
        $.growl.notice({title: 'Успешно', message: "Заказ оплачен", duration: 5000});
    },
    function (reason, options) { // fail
        $.growl.error({title: 'Ошибка', message: "Оплата не произошла", duration: 5000});
    });
});

JS
    );
}
?>