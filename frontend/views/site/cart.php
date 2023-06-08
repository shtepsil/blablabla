<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $items Items[]
 * @var $sets Sets[]
 * @var $address \common\models\UserAddress[]
 */
use common\components\Debugger as d;
use common\models\BonusSettings;
use common\models\Items;
use common\models\Orders;
use common\models\Sets;
use yii\db\ActiveQuery;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;

$context = $this->context;

$type_handling = Yii::$app->session->get('type_handling', []);
$address = [];
$sum = $sum_normal = 0;
$i = 0;
$is_weight = false;
if (ORDER_DEBUG_RES)
    echo d::res();
?>
<div class="Cart padSpace">
    <a href="/" class="backpage"><span>Вернуться к покупкам</span></a>
    <?= Html::beginForm(['site/order'], 'post', ['class' => 'f_Cart padSpace reverse', 'id' => 'basket_form']) ?>
    <?= $this->render('//blocks/cart_steps') ?>
    <h1 class="title">Корзина</h1>
    <div class="cartList" id="cart_list">
        <? if (!$items && !$sets): ?>
            <div class="cartGoods">
                <div class="cG_center">
                    <div class="title">
                        Корзина пуста
                    </div>
                </div>
            </div>
        <? else: ?>
            <?php foreach ($items as $item): ?>
                <?php
                $count = $context->cart_items[$item->id];

                /*
                 * Вот тут в самом последнем else просто запускается $item->sum_price($count)
                 * с переданным $count.
                 */
                $item_sum = $context->function_system->full_item_price(0, $item, $count);
                $sum_normal += $item->sum_price($count);
                $sum += $item_sum;
                if ($item->measure != $item->measure_price) {
                    $is_weight = true;
                }
                ?>
                <div class="cartGoods" id="items-<?= $item->id ?>">
                    <a href="<?= $item->url() ?>" class="image" style="background-image: url(<?= $item->img() ?>);">
                        <? if ($item->article): ?>
                            <span>Арт.
                                <?= $item->article ?>
                            </span>
                        <? endif ?>
                    </a>
                    <div class="cG_center">
                        <a class="title" href="<?= $item->url() ?>" target="_blank"><?= $item->name ?></a>
                        <? if ($item->body_small): ?>
                            <div class="descript">
                                <?= $item->body_small ?>
                            </div>
                        <? endif ?>
                        <? if ($item->weight): ?>
                            <div class="weight">
                                <?= $item->weight ?> кг.
                            </div>
                        <? endif ?>

                        <? if ($item->itemsTypeHandlings): ?>
                            <div class="string">
                                <?php foreach ($item->itemsTypeHandlings as $item_handling): ?>
                                    <?php
                                    $checked = false;
                                    if (!$item_handling->typeHandling->isVisible) {
                                        continue;
                                    }
                                    $checked = (isset($type_handling[$item->id]) && in_array($item_handling->typeHandling->id, $type_handling[$item->id]));
                                    ?>
                                    <p>
                                        <input type="radio" value="<?= $item_handling->typeHandling->id ?>" <?= ($checked) ? 'checked' : '' ?> id="item_handling_<?= $item_handling->id ?>" name="type_handling[<?= $item->id ?>][]">
                                        <label for="item_handling_<?= $item_handling->id ?>">
                                            <?= $item_handling->typeHandling->name ?>
                                        </label>
                                    </p>
                                <?php endforeach; ?>
                            </div>
                        <? endif ?>
                        <div class="string">
                            <span class="cG_delete delete_basket" data-id="<?= $item->id ?>">Удалить</span>
                        </div>
                    </div>
                    <div class="cG_right">
                        <div class="inputWrapper">
                            <div class="btnMinus"></div>
                            <div class="btnPlus"></div>
                            <input type="text" value="<?= $count ?>" name="items[<?= $item->id ?>]" data-type="items"
                                data-id="<?= $item->id ?>" data-measure="<?= $item->measure ?>" readonly="" />
                            <span>
                                <?= ($item->measure) ? 'шт' : 'кг' ?>.
                            </span>
                        </div>
                        <div class="price">
                            <?= number_format($item_sum, 0, '', ' ') ?> т.
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php foreach ($sets as $item): ?>
                <?php
                $count = $context->cart_sets[$item->id];
                $item_sum = round($count * $item->real_price());
                $sum_normal += $item_sum;
                $sum += $item_sum;
                ?>
                <div class="cartGoods" id="sets-<?= $item->id ?>">
                    <a class="image" style="background-image: url(<?= $item->img ?>);"
                        href="<?= Url::to(['site/set', 'id' => $item->id]) ?>">
                    </a>
                    <div class="cG_center">
                        <a href="<?= Url::to(['site/set', 'id' => $item->id]) ?>" class="title"><?= $item->name ?></a>
                        <div class="string">
                            <span class="cG_delete delete_basket" data-id="<?= $item->id ?>">Удалить</span>
                        </div>
                    </div>
                    <div class="cG_right">
                        <div class="inputWrapper">
                            <div class="btnMinus"></div>
                            <div class="btnPlus"></div>
                            <input type="text" value="<?= $count ?>" name="sets[<?= $item->id ?>]" data-type="sets"
                                data-id="<?= $item->id ?>" data-measure="1" readonly="" />
                            <span>шт.</span>
                        </div>
                        <div class="price">
                            <?= number_format($item_sum, 0, '', ' ') ?> т.
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <? endif; ?>
    </div>
    <? if ($items || $sets): ?>
        <?
        if (!Yii::$app->user->isGuest && doubleval(Yii::$app->user->identity->discount)) {
            $order = new Orders(['discount' => Yii::$app->user->identity->discount . '%']);
            $sum = $sum - $order->discount($sum);
        }
        $sum_full = $sum;
        $sum_full = $sum + ($delivery > 0 ? $delivery : 0);
        //$delivery = $context->function_system->delivery_price($sum_full, $context->city);
        $discount_price = ($sum_normal - $sum);
        if (!$discount_price) {
            $discount_price = 0;
        }
        ?>
        <div class="cartRight" id="cart_right">
            <div class="wrapperFixedPosition" id="wrap_fixed">
                <div class="cartOrder">
                    <div class="cartLines">
                        <div class="cartLine">
                            <span>Товаров на сумму</span>
                            <div class="cL_right"><b class="basket_sum">
                                    <?= number_format($sum + $discount_price, 0, '', ' ') ?> т.
                                </b></div>
                        </div>
                        <div class="cartLine <?= ($discount_price) ? '' : 'hidden' ?>" id="discount_block">
                            <span>Скидка</span>
                            <div class="cL_right"><b class="basket_sum_discount">
                                    <?= number_format($discount_price, 0, '', ' ') ?> т.
                                </b></div>
                        </div>
                        <!--                            <div class="cartLine hide">-->
                        <!--                                <span>Доставка</span>-->
                        <!--                                <div class="cL_right delivery_price">-->
                        <?php
                        //                                    if ($delivery > 0) {
//                                        echo '<b>' . number_format($delivery, 0, '', ' ') . ' т.</b>';
//                                    }
//                                    elseif ($delivery == 0) {
//                                        echo '<i class="free">Бесплатная</i>';
//                                    }
//                                    else {
//                                        echo 'Только самовывоз или яндекс доставка';
//                                    }
//                                    ?>     <!--</i></div>-->
                        <!--                            </div>-->
                        <div class="cartLine">
                            <span>Всего</span>
                            <div class="cL_right"><b class="basket_sum_full">
                                    <?= number_format($sum_full, 0, '', ' ') ?> т.
                                </b></div>
                        </div>
                        <div class="cartLine text_is_weight <?= ($is_weight ? '' : 'hidden') ?>"><?= $context->settings->get('delivery_text_weight') ?></div>
                    </div>
                    <div class="cartOrderControl">
                        <button class="btn_Form blue" type="submit">Оформить заказ</button>
                    </div>
                </div>
                <? if (!Yii::$app->user->isGuest): ?>
                    <div class="cartBalance">
                        <div class="sometext">
                            <p><b>У вас
                                    <?= (int) $context->user->bonus ?> бонусов
                                </b></p>
                            <?
                            $percent_bonus = $context->function_system->percent();
                            $add_bonus = floor(((int) $sum * ($percent_bonus)) / 100)
                                ?>
                            <p>Мы начислим вам <b class="add_bonus">
                                    <?= $add_bonus ?>
                                </b> бонусов за этот заказ</p>
                        </div>
                    </div>
                <? endif ?>

                <!--<? if (Yii::$app->user->isGuest): ?>
                        <div class="cartBlock" id="fast_cart_order">
                            <div class="title">Вы можете оформить заказ без регистрации</div>
                            <div class="text">Оставьте номер мобильного, и наш менеджер свяжется с вами в течении 10 минут</div>
                            <div class="string" id="fast_order_phone_cart">
                                <label>Ваш телефон</label>
                                <?= MaskedInput::widget([
                                    'name' => 'fast_order[phone]',
                                    'mask' => '+7(999)-999-9999',
                                    'definitions' => [
                                        'maskSymbol' => '_'
                                    ],
                                    'options' => [
                                        'class' => ''
                                    ]
                                ]); ?>
                            </div>
                            <div class="string">
                                <span class="btn_buyToClick fast_cart_order">Купить в один клик</span>
                            </div>
                        </div>
                    <? endif ?>-->
            </div>
        </div>
    <? endif; ?>
    <?= Html::endForm() ?>
</div>
<?php
$url_cart = Url::to(['site/cart']);
$url_api_cart = Url::to(['api/cart']);
$url_cart_fast = Url::to(['site/send-form', 'f' => 'fast_order']);
$this->registerJs(<<<JS
//JS
    
$('.fast_cart_order').on('click', function (e) {
    e.preventDefault();
    var element = $('input', '#fast_order_phone_cart');
    if ($.trim(element.val()) == '') {
        $('#fast_order_phone_cart').addClass('error');
        if (!$(element).data('tooltipster-ns')) {
            $(element).tooltipster({
                content: 'Необходимо заполнить телефон!'
            });
        } else {
            $(element).tooltipster('content', 'Необходимо заполнить телефон!');
            $(element).tooltipster('enable');
        }
    } else {
        var request_data = $('input', '#fast_cart_order').serializeArray();
        var add_data = {name: 'fast_order[type]', value: '2'};
        request_data.push(add_data);
        $('#fast_order_phone_cart').removeClass('error');
        if ($(element).data('tooltipster-ns')) {
            $(element).tooltipster('disable');
        }
        $('#loader').show();
        $.ajax({
            url: '{$url_cart_fast}',
            type: 'POST',
            dataType: 'JSON',
            data: request_data,
            success: function (data_return) {
                if (typeof data_return.errors != 'undefined') {
                    var errors = data_return.errors;
                    if ($.isArray(errors['fast_order-phone'])) {
                        $('#fast_order_phone_cart').addClass('error');
                        if (!$(element).data('tooltipster-ns')) {
                            $(element).tooltipster({
                                content: errors['fast_order-phone'][0]
                            });
                        } else {
                            $(element).tooltipster('content', errors['fast_order-phone'][0]);
                            $(element).tooltipster('enable');
                        }
                        $.growl.error({title: 'Ошибка', message: errors['fast_order-phone'][0], duration: 5000});
                    } else {
                        $.growl.error({title: 'Ошибка', message: "Произошла ошибка на стороне сервера!", duration: 5000});
                    }
                    $('#loader').hide();
                }
                if (typeof data_return.js != 'undefined') {
                    eval(data_return.js)
                }
                if (typeof data_return.url != 'undefined') {
                    window.location = data_return.url;
                }
            },
            error: function () {
                $('#loader').hide();
                $.growl.error({title: 'Ошибка', message: "Произошла ошибка на стороне сервера!", duration: 5000});
            }
        });
    }

})
$('#basket_form').on('click', '.delete_basket', function (e) {
    var id = $(this).data('id');
    $(this).parents('.cartGoods').remove();
    update_order_price()
}).on('change', 'input[data-id]', function (e) {
    update_order_price()
});
$('.inputWrapper').on('click', '.btnPlus', function (e) {
    var inp = $('input', $(this).parents('.inputWrapper'));
    var inpVal = $(inp).val();
    var measure = $(inp).data('measure');
    var id = $(inp).data('id');
    if (typeof measure == 'undefined' || measure == 1) {
        $(inp).val(+inpVal + 1);
    } else if (measure == 0) {
        var float = /^(\d+\.0)$/;
        var val = parseFloat(+inpVal) + 0.1;
        val = val.toFixed(1);
        if (float.test(val)) {
            val = parseInt(val);
        }
        $(inp).val(val);
    }
    update_order_price()

}).on('click', '.btnMinus', function (e) {
    var inp = $('input', $(this).parents('.inputWrapper'));
    var inpVal = $(inp).val();
    var measure = $(inp).data('measure');
    var id = $(inp).data('id');
    if (typeof measure == 'undefined' || measure == 1) {
        if (inpVal > 1) {
            $(inp).val(+inpVal - 1);
        }
    } else if (measure == 0) {
        if (inpVal > 0.1) {
            var float = /^(\d+\.0)$/;
            var val = parseFloat(+inpVal) - 0.1;
            val = val.toFixed(1);
            if (float.test(val)) {
                val = parseInt(val);
            }
            $(inp).val(val);
        }
    }
    update_order_price()

}).on('change', 'input', function (e) {
    var measure = $(this).data('measure');
    var val = $(this).val();
    var inpVal = $(this).val();
    var id = $(this).data('id');
    if (typeof measure == 'undefined' || measure == 1) {
        if (inpVal > 1) {
            var float_no = /^(\d+\.\d+)$/;
            if (float_no.test(val)) {
                val = parseInt(val);
                $(this).val(val);
            }
        } else {
            val = 1;
            $(this).val(val);
        }
    } else if (measure == 0) {
        if (inpVal > 0.1) {
            var float = /^(\d+\.0)$/;
            val = parseFloat(+inpVal);
            val = val.toFixed(1);
            if (float.test(val)) {
                val = parseInt(val);
                $(this).val(val);
            }
        } else {
            val = 0.1;
            $(this).val(val);
        }
    }
    update_order_price()

});
function update_order_price() {
    var res = $('.res');
    var request_data = $('#basket_form').serializeArray();
    var add_data = {name: 'action', value: 'editBasket'};
    request_data.push(add_data);
    request_data.push({name: 'city', value: {$context->city}});
    $.ajax({
        url: '{$url_api_cart}',
        type: 'GET',
        dataType: 'JSON',
        data: request_data,
        success: function (data) {
            res.html('Done<br><pre>' + prettyPrintJson.toHtml(data) + '</pre>');
            if (!data.count) {
                //location.reload();
            } else {
                edit_basket(data.items, 'items');
                edit_basket(data.sets, 'sets');
                $('.basket_sum').text(data.sum);
                $('.basket_sum_full').text(data.sum_full);
                $('.delivery_price').html(data.delivery);
                $('.add_bonus').text(data.add_bonus);
                if (typeof data.discount_price != 'undefined') {
                    $('.basket_sum_discount').html(data.discount_price);
                    if ($('#discount_block').hasClass('hidden')) {
                        $('#discount_block').removeClass('hidden')
                    }
                } else {
                    if (!$('#discount_block').hasClass('hidden')) {
                        $('#discount_block').addClass('hidden')
                    }
                }
            }
        },
        error: function (data) {
            res.html('Fail<br>' + JSON.stringify(data));
        }
    });
}
function edit_basket(items, type) {
    $.each(items, function (id, el) {
        if (typeof el.price_full != 'undefined') {
            $('.price', '#' + type + '-' + id).html(el.price_full);
        }
    })
}

// Фиксирование блока
cart_fixed_block();
JS
)
    ?>