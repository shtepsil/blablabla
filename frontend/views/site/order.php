<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $items Items[]
 * @var $sets Sets[]
 * @var $a_address \common\models\UserAddress[]
 */

use common\components\Debugger as d;
use common\models\BonusSettings;
use common\models\City;
use common\models\Items;
use common\models\Orders;
use common\models\Sets;
use common\models\UserAddress;
use frontend\form\Order;
use frontend\widgets\ActiveField;
//use frontend\widgets\ActiveForm;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use common\helpers\DateHelper;
use frontend\widgets\Modal;
use frontend\assets\WidgetCloudpaymentsAsset;

//use yii\bootstrap\Modal;

$context = $this->context;
$city = $context->city;
$no_address = true;
$user = $context->user;
$model = new Order();
$form_name = strtolower($model->formName());
$data_address = [];
$data_phones = [];
$a_address = [];
if ($user) {
    $a_address = UserAddress::find()->andWhere(['user_id' => $user->id])->orderBy(['isMain' => SORT_DESC])->all();
    if ($a_address) {
        $no_address = false;
        $start = true;
        $data_address = ArrayHelper::map(
            $a_address,
            function ($el) {
                return $el->id;
            },
            function ($el) use (&$data_phones, &$city, &$start) {
                /**
                 * @var $el UserAddress
                 */
                if ($start == true) {
                    $city = $el->city;
                }
                $data_phones[$el->id] = [
                    'city' => $el->city,
                    'phone' => $el->phone
                ];
                return 'г.' . $el->data_city[$el->city] . ', ул. ' . $el->street . ', дом. ' . $el->home . (($el->house) ? (', кв. ' . $el->house) : '');
            }
        );
        $data_address['none'] = 'Другой';
    }
}

$sum = $i = $sum_normal = 0;

$info = Json::decode($cityInfo);
$info = $info[$cityId];

$isWholesale = (isset($context->user->isWholesale)) ? $context->user->isWholesale : 0;

WidgetCloudpaymentsAsset::register($this);

//d::pri($info);

?>

<script src="https://api-maps.yandex.ru/2.1/?apikey=a800cece-8fcc-4d1c-bc91-da2061eb8d3e&lang=ru_RU"
    type="text/javascript"></script>
<div class="Cart padSpace">
    <a href="<?= Url::to(['site/basket']) ?>" class="backpage"><span>Вернуться к корзине</span></a>
    <?php
    $model->city = $city;
    if ($user) {
        $model->email = $user->email;
        if (!$no_address) {
            $model->phone = $a_address[0]->phone;
        } else {
            $model->phone = $user->phone;
        }
    }
    $model->code = Yii::$app->session->get('invited_code');
    if (\Yii::$app->user->isGuest) {
        //            $model->scenario = 'isGuest';
    } else {
        if ($no_address) {
            //                $model->scenario = 'no_address';
        } else {
            //                $model->scenario = 'is_address';
        }
    }
    $form = ActiveForm::begin([
        'action' => Url::to(['site/send-form', 'f' => 'order']),
        'enableAjaxValidation' => false,
        'validateOnSubmit' => false,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'options' => ['enctype' => 'multipart/form-data', 'id' => 'order', 'class' => 'formOrder f_Cart padSpace reverse f_Order'],
        'fieldClass' => ActiveField::className(),
        'fieldConfig' => [
            'required' => false,
            'options' => ['class' => 'col'],
            'template' => <<<HTML
{label}
{input}
HTML
            ,
        ],
    ]);
    ?>
    <?= $this->render('//blocks/cart_steps') ?>
    <h1 class="title">Оформление заказа</h1>
    <? if (ORDER_DEBUG_RES)
        echo d::res() ?>
        <div class="customOrTitle"><i>1</i><span>Товары в заказе</span></div>
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
                $item_sum = $context->function_system->full_item_price($discount, $item, $count);
                $sum_normal += $item->sum_price($count);
                $handling = [];
                if (isset($type_handling_session[$item->id])) {
                    $handling = array_flip($type_handling_session[$item->id]);
                }
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
                                <?php
                                $handing_string = '';
                                foreach ($item->itemsTypeHandlings as $item_handling) {
                                    if (!$item_handling->typeHandling->isVisible || !isset($handling[$item_handling->type_handling_id])) {
                                        continue;
                                    }
                                    $handing_string .= '<p><span class="ordered">' . $item_handling->typeHandling->name . '</span></p>';
                                }
                                if ($handing_string) {
                                    echo $handing_string;
                                }
                                ?>
                            </div>
                        <? endif ?>
                    </div>
                    <div class="cG_right">
                        <div class="numSize">
                            <?= $count ?>
                            <?= ($item->measure) ? 'шт' : 'кг' ?>.
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
                    <div class="image" style="background-image: url(<?= $item->img ?>);">
                    </div>
                    <div class="cG_center">
                        <a href="<?= Url::to(['site/set', 'id' => $item->id]) ?>" class="title"><?= $item->name ?></a>
                    </div>
                    <div class="cG_right">
                        <div class="numSize">
                            <?= $count ?> шт.
                        </div>
                        <div class="price">
                            <?= number_format($item_sum, 0, '', ' ') ?> т.
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <? endif; ?>
        <?
        if (!Yii::$app->user->isGuest && doubleval(Yii::$app->user->identity->discount)) {
            $order = new Orders(['discount' => Yii::$app->user->identity->discount . '%']);
            $sum = $sum - $order->discount($sum);
        }
        $sum_full = $sum + ($delivery > 0 ? $delivery : 0);
        //$delivery = $context->function_system->delivery_price($sum_full, $context->city);
        $discount_price = ($sum_normal - $sum);
        if (!$discount_price) {
            $discount_price = 0;
        }
        ?>
        <div class="customOrTitle"><i>2</i><span>Оплата</span></div>
        <div class="cartGoods">
            <div class="string">
                <?php if (!empty($paymentType)): ?>
                    <?= $form->field($model, 'payment', [
                        'template' => '{label}<div class="blSelect payment">{input}</div>'
                    ])->dropDownList($paymentType); ?>
                <?php else: ?>
                    нет доступных способов оплаты
                <?php endif; ?>
            </div>
        </div>
        <div class="customOrTitle"><i>3</i>
            <? if ($context->function_system->only_pickup): ?>
                <? $model->type_delivery = 0; ?>
                <span>Место забора</span>
            <? else: ?>
                <? $model->type_delivery = 1; ?>
                <span>Куда и кому доставить</span>
            <? endif; ?>
        </div>

        <?php
        $class_courier = $style = $yandex_active = '';
        $disabled = $checked = $checked_ = false;
        $style_yandex = 'none';
        $js_weekend = $js_yandex_click = $js_pickup_click = '0'; // Сделал строкой, потому что из PHP в js булево не передать.
        $text = $info['text_courier'];

        if (!$info['isYandexDelivery']) {
            $style_yandex = 'display:block';
            $js_pickup_click = '1';
        }

        if ($info['only_pickup']) {
            $style = 'display:none;';
        }
        /*
         * Если минимальная цена для бесплатной доставки больше нуля
         * и больше цены корзины, то доставка курьером будет недоступна.
         */elseif ($info['courier_price'] > 0 && $info['courier_price'] > $sum) {
            $class_courier = ' disabled';
            $disabled = true;
            $text = $info['courier_price_text'];
        }
        // Если цена корзины достаточна для бесплатной доставки курьером
        elseif ($info['delivery_price'] === 0 || empty($pickpoints)) {
            $class_courier = ' active';
            $checked = true;
        }

        /*
         * Если текущий день суббота или воскресенье,
         * то по умолчанию будет выбрана yandex доставка.
         */
        if (DateHelper::weekendCheck()) {
            /*
             * Это попытки задать установки при загрузке страницы.
             * Не разобрался, в итоге сделал trigger click на yandex доставке
             */
            //            $yandex_active = ' active';
//            $class_courier = '';
//            $checked = false;
//            $checked_ = true;
            $js_weekend = $js_yandex_click = '1';
        }

        $delivery_block_off = 'display:block;'; // Строка доставка недоступна
        $delivery_block_style = 'display:none;'; // Блок информации доставки
        $style_pickup = 'display:none;'; // Самовывоз
        $style_yandex = 'display:none;'; // Яндекс доставка
        
        if ($info['pickup_switcher']) {
            $style_pickup = 'display:block;';
            $delivery_block_style = 'display:block;';
            $delivery_block_off = 'display:none;';
        }

        if ($info['isYandexDelivery']) {
            $style_yandex = 'display:block;';
            $delivery_block_style = 'display:block;';
            $delivery_block_off = 'display:none;';
        }

        ?>
        <div class="cartGoods">
            <?= Html::activeHiddenInput($model, 'isWholesale', ['value' => $isWholesale]) ?>
            <?= Html::activeHiddenInput($model, 'type_delivery') ?>
            <?= Html::activeHiddenInput($model, 'only_pickup') ?>
            <div class="string select_address">
                <?= $form->field($model, 'city', [
                    'template' => '{label}<div class="blSelect">{input}</div>'
                ])->dropDownList($context->function_system->data_city_view); ?>
            </div>
            <div class="delivery-off" style="<?= $delivery_block_off ?>">
                В выбранном городе, доставка временно недоступна.
                <br><br>
            </div>
            <div class="delivery-info-block" style="<?= $delivery_block_style ?>">
                <? if ($model->type_delivery == 1): ?>
                    <?php
                    $pickpoints = ($info ? $info['pickpoint'] : null); ?>
                    <div class="row<?= ($info['delivery_price'] !== 0 ? ' active' : '') ?> pickup-radio"
                        style="<?= $style_pickup ?>">
                        <div class="col-md-1">
                            <?= Html::radio($form_name . '[delivery]', (!empty($pickpoints) && $info['delivery_price'] !== 0 ? true : false), [
                                'value' => 1,
                                'autocomplete' => 'off',
                                'id' => 'pick-up'
                            ]) ?>
                            <?= Html::label('Самовывоз') ?>
                            <?php
                            if (!empty($pickpoints)) {
                                $coord = $info['coordinate'];
                                $this->registerJs(<<<JS
ymaps.ready(initStart);

// Костыль, для выпадающего списка поля поиска на карте yandex
setInterval(function(){
    $('ymaps[style*=40000]').css({width: '217px'});
}, 1000);

function initStart() {
    cl('ymap-initStart 360');
    var myMap = new ymaps.Map("map", {
            center: [$coord],
            zoom: 10
        }, {
            searchControlProvider: 'yandex#search'
        });
JS
                                ); // registerJs
                                ?>
                                <div class="popup-block">
                                    <?= Modal::widget([
                                        'id' => 'popupPickUp',
                                        'toggleElement' => [
                                            // 'tag' => 'div',
                                            'class' => 'select-city',
                                            'label' => '<span>Выбрать пункт</span>'
                                        ],
                                        'body' => $this->render('//popups/pick_up', [
                                            'pickpoints' => $pickpoints
                                        ]),
                                    ]); ?>
                                </div>
                                <?
                                $this->registerJs(<<<JS
// Закрывающяя скобка для function initStart()
}
JS
                                ); // registerJs
                                ?>
                            <?php } // if (!empty($pickpoints))
                            ?>
                        </div>
                    </div>
                    <div id="courier" class="row<?= $class_courier ?>" style="<?= $style ?>">
                        <div class="col-md-4">
                            <?= Html::radio($form_name . '[delivery]', $checked, [
                                'value' => 2,
                                'autocomplete' => 'off',
                                'disabled' => $disabled
                            ]) ?>
                            <?= Html::label('Курьером до двери') ?>
                            <p>
                                <?= $text ?>
                            </p>
                        </div>
                    </div>
                    <?= Modal::widget([
                        'id' => 'weekends_delivery',
                        'body' => $this->render('//popups/weekends_delivery'),
                    ]); ?>
                    <div id="yandex_delivery" class="row yandex-radio<?= $yandex_active ?>" style="<?= $style_yandex ?>">
                        <div class="col-md-4">
                            <?= Html::radio($form_name . '[delivery]', $checked_, [
                                'value' => 3,
                                'autocomplete' => 'off',
                            ]) ?>
                            <?= Html::label('Курьер до двери<br>ЯндексДоставка') ?>
                            <p>
                                <?= 'Доставка в течении 2-x часов' ?>
                            </p>
                        </div>
                    </div>

                    <?php if ($pickpoints) {
                        $currentPickpoint = current($pickpoints);
                    } ?>
                    <?= Html::hiddenInput($form_name . '[our_stories_id]', ($pickpoints ? $currentPickpoint['id'] : ''), [
                        'id' => 'order-our_stories_id'
                    ]) ?>
                    <? if ($user): ?>
                        <? if ($data_address): ?>
                            <div class="string delivery-address">
                                <label>Адрес доставки</label>
                                <div class="blSelect payment">
                                    <?= Html::activeDropDownList($model, 'address_id', $data_address) ?>
                                </div>
                            </div>
                        <? endif ?>
                    <? endif; ?>
                    <?
                    if (!$no_address) {
                        $class_address = 'hidden';
                        $json_phones = Json::encode($data_phones);
                        $this->registerJs(<<<JS
    //JS
    $("#{$form_name}-address_id").chosen({disable_search_threshold: 10});
    var data_phones = {$json_phones}
    $("#{$form_name}-address_id").on('change', function (e) { 
        if ($(this).val() == 'none') {
            $('.select_address').removeClass('hidden');
            
            only_pickup();
        } else {
            check_address_user($(this).val());
            
            $("#{$form_name}-phone").val(data_phones[$(this).val()].phone)
            if ($('.select_address').is(':visible')) {
                $('.select_address').addClass('hidden');
            }
        }
    })
JS
                        );
                    } else {
                        $class_address = '';
                    }

                    $text_default_pickup = Json::encode($context->settings->get('delivery_text_no_delivery'));
                    $this->registerJs(<<<JS
    //JS
    var city_pickup = {$cityInfo};
    
    
    var order_id = $('#order-city').val();
    
    if (city_pickup[order_id].isYandexDelivery != 1) {
        $('#yandex_delivery').css('display','none'); 
    } else { 
       $('#yandex_delivery').css('display','block'); 
        
       
    }
    
    function check_address_user(val) {
        var city = city_pickup[data_phones[val].city];
        
        if (city.only_pickup == 1) {
            $('.only_pickup').removeClass('hidden');
            $('#{$form_name}-only_pickup').val(1);
        } else {
            $('.only_pickup').addClass('hidden');
            $('#{$form_name}-only_pickup').val(0);
            
            if (city.id == 1 && $('.time-block').hasClass('hidden')) {
                $('.time-block').removeClass('hidden');
            }
            else if (!$('.time-block').hasClass('hidden')) {
                $('.time-block').addClass('hidden');
            }
            
            if (city.pickpoint !== null) {
//                $('.delivery-address').hide();
                $('#order-type_delivery').val(0);
                
                for (var i in city.pickpoint) {
                    $('#cart_right .cartLine:nth-child(4) .delivery_price b').html(city.pickpoint[i].delivery);
                    $('#cart_right .cartLine:nth-child(5) .basket_sum_full').html(city.pickpoint[i].sum_all);
                    
                    break;
                }
            }
            else {
                if (city.delivery_price == 0) {
                    var delivery = '<i class="free">'+city.delivery+'</i>';
                }
                else {
                    var delivery = city.delivery;
                }
                
                $('#cart_right .cartLine:nth-child(4) .delivery_price b').html(delivery);
            }
        }
    
        if ($('.select_address:not(.address_delivery)').hasClass('hidden')) {
            $('.select_address:not(.address_delivery)').removeClass('hidden');
        }
        else if ($('.select_address:not(.address_delivery)').is(':hidden')) {
            $('.select_address:not(.address_delivery)').show();
        }
    }
    
    function only_pickup() {
        var city = city_pickup[$("#{$form_name}-city").val()];
        
        if (city.only_pickup == 1) {
            $('#{$form_name}-only_pickup').val(1);
            $('.address_delivery').addClass('hidden');
            $('.only_pickup').removeClass('hidden');
            
            $('#cart_right .cartLine:nth-child(4) .delivery_price b').html(city.pickup_price);
            $('#cart_right .cartLine:nth-child(5) .basket_sum_full').html(city.pickup_sum_all);
        } else {
            $('#{$form_name}-only_pickup').val(0);
            
            if ($('input#pick-up').prop('checked')) {
                if (city.pickpoint !== null && (!$('.select_address').hasClass('hidden') || $('.select_address').is(':visible'))) {
                    if (city.id == 1 && $('.time-block').hasClass('hidden')) {
                        $('.time-block').removeClass('hidden');
                    }
                    else if (!$('.time-block').hasClass('hidden')) {
                        $('.time-block').addClass('hidden');
                    }
                }
                
                $('#order-type_delivery').val(0);
            }
            else {
                $('#order-type_delivery').val(1);
            }
            
            if (city.pickpoint !== null && $(".cartGoods").find('.row').eq(0).find('input').prop('checked')) {
                for (var i in city.pickpoint) {
                    $('#cart_right .cartLine:nth-child(4) .delivery_price b').html(city.pickpoint[i].delivery);
                    $('#cart_right .cartLine:nth-child(5) .basket_sum_full').html(city.pickpoint[i].sum_all);              
                    break;
                }
            }
            else {
                var delivery = city.pickup_price;
                var sum = city.pickup_sum_all;
                
                if (city.delivery_price > 0) {
                    delivery = city.delivery;
                    sum = city.sum_all;
                } else if (city.delivery_price == 0) {
                    delivery = '<i class="free">'+city.delivery+'</i>';
                    sum = city.sum_all;
                }
         
                $('#cart_right .cartLine:nth-child(4) .delivery_price b').html(delivery);
                $('#cart_right .cartLine:nth-child(5) .basket_sum_full').html(sum);
            }
        }
        
        if ($('.select_address').hasClass('hidden')) {
            $('.select_address').removeClass('hidden');
        }
        else if ($('.select_address').is(':hidden')) {
            $('.select_address').show();
        }
        
        if (!$('.address_delivery').hasClass('hidden') && $(".cartGoods").find('.row').eq(0).find('input').prop('checked')) {
            $('.address_delivery').addClass('hidden');
            
        }
        else if ($('.address_delivery').hasClass('hidden') && $(".cartGoods").find('.row').eq(1).find('input').prop('checked')) {
            $('.address_delivery').removeClass('hidden');
        }
    }
    
    var today = new Date();
    
    if(today.getDay() == 0) {
            $('<span style="margin: 5px 0;margin-bottom:10px;font-size:16px;display:inline-block;font-family:Proxima Nova;">Или вы можете забрать товар самовывозом из <a href="https://kingfisher.kz/contacts.html#map" target="blank">наших магазинов</a></span><br/>').prependTo($('.select_address')[1]);
            $('<span style="color:red;margin:10px 0;margin-top:-10px;font-size:16px;display:inline-block;font-family:Proxima Nova;">В воскресенье доставка курьером не работает. Ваш заказ будет доставлен в понедельник.</span><br/>').prependTo($('.select_address')[1]);
    }
    
    if ($("#{$form_name}-city").is(':visible')) {
        only_pickup();
        var select_cur_city = $("#{$form_name}-city"),
            wrap_min_order_sum = $('.wrap-min-order-sum');
        setTimeout(function(){
            var current_city = city_pickup[select_cur_city.val()],
                min_order_sum_current_city = current_city.min_order_sum;

            if(min_order_sum_current_city){
                wrap_min_order_sum.fadeIn(100)
                    .find('.min-order-sum-city b:first-child')
                    .html(number_format(min_order_sum_current_city, 0, '', ' '));
            }else{
                wrap_min_order_sum.hide().find('.min-order-sum-city b:first-child').html('0');
            }
        }, 400);
    } else {
        check_address_user($("#{$form_name}-address_id").val());
    }

    // cl(city_pickup[1]);
    $("#{$form_name}-city").on('change', function (e) {
        var current_city = city_pickup[$(this).val()],
			delivery_off = $('.delivery-off'),
			delivery_block_info = $('.delivery-info-block'),
            pickup_radio = $('.pickup-radio'),
            yandex_radio = $('.yandex-radio'),
            pickup_switcher = Boolean(Number(current_city.pickup_switcher)),
            isYandexDelivery = Boolean(Number(current_city.isYandexDelivery)),
            wrap_min_order_sum = $('.wrap-min-order-sum'),
            min_order_sum_current_city = current_city.min_order_sum;
        
        $('#cart_right .cartOrderControl button[type=submit]').removeAttr('disabled');
        
        if ($(".cartGoods").find('.row').eq(0).hasClass('disabled')) {
            $(".cartGoods").find('.row').eq(0).removeClass('disabled');
            $(".cartGoods").find('.row').eq(0).find('input').removeAttr('disabled');
        }
        
        if ($(".cartGoods").find('.row').eq(1).hasClass('disabled')) {
            $(".cartGoods").find('.row').eq(1).removeClass('disabled');
            $(".cartGoods").find('.row').eq(1).find('input').removeAttr('disabled');
        }
        
        // Улаение способов оплаты (очищение seletc'a)
        $("#order-payment option").remove();
        /**
         * Установка способов оплты для выбранного города
         * загрузка новых option's для выбранного города
         */
        for (var i in current_city.payment) {
            $("#order-payment")
             .append($("<option></option>")
                        .attr("value", i)
                        .text(current_city.payment[i])
             );
        }

        if(min_order_sum_current_city){
            wrap_min_order_sum.fadeIn(100).find('.min-order-sum-city b:first-child').html(number_format(min_order_sum_current_city, 0, '', ' '));
        }else{
            wrap_min_order_sum.hide().find('.min-order-sum-city b:first-child').html('0');
        }

        // Обновление html списка способов оплаты
        $("#order-payment").trigger("chosen:updated");
        
        if (current_city.only_pickup != 1) {
            $('.address_delivery').removeClass('hidden');
            $('.only_pickup').addClass('hidden');
            $('#{$form_name}-only_pickup').val(0);
            
            if (current_city.id == 1 && $('.time-block').hasClass('hidden')) {
                $('.time-block').removeClass('hidden');
            }
            else if (!$('.time-block').hasClass('hidden')) {
                $('.time-block').addClass('hidden');
            }
        } else {
            $('#{$form_name}-only_pickup').val(1);
            $('.address_delivery').addClass('hidden');
            $('.only_pickup').removeClass('hidden');
            
            if (!$('.time-block').hasClass('hidden')) {
                $('.time-block').addClass('hidden');
            }
        }
        
        if (current_city.isYandexDelivery != 1) {
            $('#yandex_delivery').css('display','none'); 
            $('#mapyandex').css('display','none');
        } else { 
    
        //$('.ymaps-2-1-78-route-panel-input__input[placeholder*=Откуда]').val('Актобе,улица В.И. Пацаева, 38');
    
           $('#yandex_delivery').css('display','block'); 
           $('#mapyandex').css('display','block');
         //  console.log('cccccccc');
        //   $('.ymaps-2-1-78-float-button.ymaps-2-1-78-_hidden-icon').trigger('click');	
        }
        
        if (current_city.pickpoint !== null) {
            $(".cartGoods").find('.row').eq(0).find('input').prop('checked', true);
            $(".cartGoods").find('.row').eq(0).show();
            $(".cartGoods").find('.row').eq(1).hide();
            $(".cartGoods").find('.row').eq(0).find('.popup-block').show();
            changeDeliveries(current_city);// ! тут делаем выбранным пункт курьером
            var delivery_clone = $('#delivery_clone').html();

            /**
             * Если "Стоимость доставки (общая)" указан хотяб 1тг или более.
             */
            if ($(".cartGoods").find('.row').eq(0).find('input').prop('checked') && 
                $(".cartGoods").find('.row').eq(0).find('input').val() == 1) {
                if (current_city.pickup_price > 0) {
                    $('#cart_right .cartLine:nth-child(4) .delivery_price b').html(current_city.pickup_price);
                    $('#cart_right .cartLine:nth-child(5) .basket_sum_full').html(current_city.pickup_sum_all);
                }
                else {
                    if (current_city.pickup_price > 0) {
                        $('#cart_right .cartLine:nth-child(4) .delivery_price b').html(current_city.pickup_price);
                        $('#cart_right .cartLine:nth-child(5) .basket_sum_full').html(current_city.pickup_sum_all);
                    }
                    else {
                        for (var i in current_city.pickpoint) {
                            $('#cart_right .cartLine:nth-child(4) .delivery_price b').html(current_city.pickpoint[i].delivery);
                            $('#cart_right .cartLine:nth-child(5) .basket_sum_full').html(current_city.pickpoint[i].sum_all);
                            
                            break;
                        }
                    }
                }
            }
            else {
                var delivery = current_city.pickup_price;
                var sum = current_city.pickup_sum_all;
                var yandex_delivery = false;
                
                /**
                 * Пока не проверял, но по догадке:
                 * Судя по коду, этот if сработает только тогда, 
                 * когда будет указана общие суммы доставки "ОТ ххх" и "ДО ххх"
                 * В остальных случаях в текущем блоке else, current_city.delivery_price
                 * будет либо -1 либо 0
                 */
                if (current_city.delivery_price > 0) {
                    delivery = current_city.delivery;
                    sum = current_city.sum_all;
                } else if (current_city.delivery_price == 0) {
                    if(current_city.courier_price <= current_city.sum_all_number){
                        delivery = '<i class="free">' + current_city.delivery + '</i>';
                        sum = current_city.sum_all;
                    }else{
                        // Делаем click на yandex доставке
                        $('#yandex_delivery').trigger('click');
                        yandex_delivery = true;
                        $(".cartGoods").find('.row').eq(1).addClass('disabled');
                        $(".cartGoods").find('.row').eq(1).find('input').prop('disabled', true);
                    }
                }
                $('#cart_right .cartLine:nth-child(5) .basket_sum_full').html(sum);
                $('#cart_right .cartLine:nth-child(4) .delivery_price b').html(delivery);

                if (yandex_delivery === true) {
                    $('#cart_right .cartLine:nth-child(4) .delivery_price b').html('<i class="free"><a class="free" href="#mapyandex">Введите адрес для расчета</a></i>');
                }
            }
            
            $('#order-type_delivery').val(0);
        }
        else {
            $('#order-our_stories_id').val('');
            $(".cartGoods").find('.row').eq(0).find('.popup-block').hide();
            
            var delivery = current_city.pickup_price;
            var sum = current_city.pickup_sum_all;
            
            if (current_city.only_pickup) {
                // Сейчас выключено для тестов
                //$(".cartGoods").find('.row').eq(0).find('input').prop('checked', true);
                $('#cart_right .cartOrderControl button[type=button]').prop('disabled', true);
                
                $(".cartGoods").find('.row').eq(0).show();
                $(".cartGoods").find('.row').eq(1).hide();
                
                if ($(".cartGoods").find('.row').eq(1).hasClass('active')) {
                    $(".cartGoods").find('.row').eq(1).removeClass('active');
                }
                if (!$(".cartGoods").find('.row').eq(0).hasClass('active')) {
                    $(".cartGoods").find('.row').eq(0).addClass('active');
                }
                
                delivery = current_city.delivery;
                sum = current_city.sum_all;
            } else {
                /**
                 * Если минимальная сумма заказа достаточна для бесплатной доставки курьером
                 *  то значение current_city.courier_price будет 0
                 */
                if (current_city.courier_price == 0) {
                    $(".cartGoods").find('.row').eq(1).find('input').prop('checked', true);
                }
                else {
                    //$('#cart_right .cartOrderControl button[type=submit]').prop('disabled', true);
                    $('#cart_right .cartOrderControl button[type=button]').prop('disabled', true);
                }
                
                if ($(".cartGoods").find('.row').eq(0).hasClass('active')) {
                    $(".cartGoods").find('.row').eq(0).removeClass('active');
                }

                if (!$(".cartGoods").find('.row').eq(1).hasClass('active')) {
                    $(".cartGoods").find('.row').eq(1).addClass('active');
                }
                
                if (current_city.delivery_price > 0) {
                    delivery = current_city.delivery;
                    sum = current_city.sum_all;
                } else if (current_city.delivery_price == 0) {
                    delivery = '<i class="free">'+current_city.delivery+'</i>';
                    sum = current_city.sum_all;
                }
            }
        
            $('#cart_right .cartLine:nth-child(4) .delivery_price b').html(delivery);
            $('#cart_right .cartLine:nth-child(5) .basket_sum_full').html(sum);
            
            $('#order-type_delivery').val(1);
        }
        
        if (current_city.only_pickup) {
            $(".cartGoods").find('.row').eq(1).find('p').html('');
        }
        else {
            if (current_city.courier_price > 0) {
                $(".cartGoods").find('.row').eq(1).find('p').html(current_city.courier_price_text);
            }
            else {
                $(".cartGoods").find('.row').eq(1).find('p').html(current_city.text_courier);
            }
        }
        
        $('.pickup-text').html('');

        if(pickup_switcher){
            pickup_radio.show(10, function(){
                delivery_off.hide(10, function(){
                    delivery_block_info.fadeIn(100);
                })
            });
        }else{
            pickup_radio.hide(10);
            if(!pickup_switcher && !isYandexDelivery){
                delivery_block_info.fadeOut(100, function(){
                    delivery_off.fadeIn(100);
                });
            }
        }

        if(isYandexDelivery){
            yandex_radio.show(10, function(){
                delivery_off.hide(10, function(){
                    delivery_block_info.fadeIn(100);
                })
            });
        }else{
            yandex_radio.hide();
            if(!pickup_switcher && !isYandexDelivery){
                delivery_block_info.fadeOut(100, function(){
                    delivery_off.fadeIn(100);
                });
            }
        }
    });
    
    // При клике на карте yandex на значке пункта самовывоза, происходит клик этого объекта.
    // При кликах на способы доставки запускается этот блок.
    $(document).on('click', "#cart_list .cartGoods .row:not(.disabled)", function (e) {
        e.preventDefault();
        if($(this).attr('id') !== undefined && $(this).attr('id') == 'courier' ){
            if('{$js_weekend}' == '1'){
//                popup({block_id: '#popupPickUp', action: 'open'});
//                popup({block_id: '#popupCallback', action: 'open'});
                $('#weekends_delivery').popup('open');
            }
        }
//        return;
        setDeliveryMethod(this);
    });
    
    function changeAddress(val) {
        if (val == 1) {
            $('#{$form_name}-only_pickup').val(1);
            
            if (!$('.delivery-address').hasClass('hidden')) {
                $('.delivery-address').addClass('hidden');
            }
                
            if (!$('.address_delivery').hasClass('hidden')) {
                $('.address_delivery').addClass('hidden');
            }
            
            $('#order-time_order').closest('.string').addClass('hidden');
    
            $('.address_delivery_yandex').addClass('hidden');		 
              
            $('#order-street').val('');
            $('#order-street').prop('readonly', false);
            $('#order-home').val('');	
            $('#order-home').prop('readonly', false);
            $('#order-house').val('');	
            $('#order-house').prop('readonly', false);	 	 
            
        } else if (val == 3) {
            yandexDeliveryMap();
        }
        else {
            
            $('#order-street').val('');
            $('#order-street').prop('readonly', false);
            $('#order-home').val('');	
            $('#order-home').prop('readonly', false);
            $('#order-house').val('');	
            $('#order-house').prop('readonly', false);
            
            $('.address_delivery_yandex').addClass('hidden');
            
            if ($('#order-time_order').closest('.string').hasClass('hidden')) {
                    $('#order-time_order').closest('.string').removeClass('hidden');
            }
            
            if (!$('.delivery-address').hasClass('hidden') && $('.delivery-address').is(':hidden')) {
                $('.delivery-address').show();
            }
            else if ($('.delivery-address').hasClass('hidden')) {
                $('.delivery-address').removeClass('hidden');
            }
            
            if ($('#order-address_id').length == 0) {
                if ($('.address_delivery').hasClass('hidden')) {
                    $('.address_delivery').removeClass('hidden');
                }
            }
            else {
                if (!$('.address_delivery').hasClass('hidden')) {
                    $('.address_delivery').addClass('hidden');
                }
            }
            
            if ($('.delivery-address').length == 0 && $('.select_address').hasClass('hidden')) {
                $('.select_address').removeClass('hidden');
            }
        }
    }
    
    function changeDeliveries(current_city)
    {
        var initMap = false;
        $('#order-our_stories_id').val('');
        
        if (current_city.pickpoint !== null) {
            var k = 0;
            
            var popupChoiceDeliveriPoint = $("#popupPickUp .popupBody ul");
            popupChoiceDeliveriPoint.html('');
            // Для переинициализации, сбросим содержимое Ya-карты
            $('#map').html('');

            var html = '';
            for (var i in current_city.pickpoint) {
                 if (k++ == 0) {
                    var text = '<div class="baloon-poup" data-id="' + current_city.pickpoint[i].coordinate + '">' + current_city.pickpoint[i].name+'<br><p>Заберу от сюда</p></div>';
                    $('#order-our_stories_id').val(i);
                    
                    $('#cart_right .cartLine:nth-child(4) .delivery_price b').html(current_city.pickpoint[i].delivery);
                    $('#cart_right .cartLine:nth-child(5) .basket_sum_full').html(current_city.pickpoint[i].sum_all);
                 }
                 
                 // Соберём новый список пунктов по выбранному городу
                 html += '<li data-id="' + i + '" data-content="' + current_city.pickpoint[i].coordinate + '">'
                            + current_city.pickpoint[i].name
                       + '</li>';
            }
            
            if (current_city.delivery_price == 0) {
                if ($(".cartGoods").find('.row').eq(0).hasClass('active')) {
                    $(".cartGoods").find('.row').eq(0).removeClass('active');
                }
                
                if (!$(".cartGoods").find('.row').eq(1).hasClass('active')) {
                    $(".cartGoods").find('.row').eq(1).addClass('active');
                }
                
                // !=checked ==========================================================================
                // !=checked ==========================================================================
                // !=checked ==========================================================================
                // !=checked ==========================================================================
                // !=checked ==========================================================================
                // !=checked ==========================================================================
                $(".cartGoods").find('.row').eq(1).find('input').prop('checked', true);
            }
            else {
                if ($(".cartGoods").find('.row').eq(1).hasClass('active')) {
                    $(".cartGoods").find('.row').eq(1).removeClass('active');
                }
                
                if (!$(".cartGoods").find('.row').eq(0).hasClass('active')) {
                    $(".cartGoods").find('.row').eq(0).addClass('active');
                }
                
                $(".cartGoods").find('.row').eq(0).find('input').prop('checked', 'checked');
            }
             
            if (current_city.only_pickup) {
                $(".cartGoods").find('.row').eq(1).hide();
            }
            else {
                $(".cartGoods").find('.row').eq(1).show();
            }
            
            if (k > 0) {
                changeAddress(1);
            }
            
            // Список пунктов по городу вставим в ul
            popupChoiceDeliveriPoint.html(html);
            
            ymaps.ready(initReload);
        
            function initReload() {
                var coord = current_city.coordinate.split(',');
                
                var myMap = new ymaps.Map("map", {
                        center: [coord[0], coord[1]],
                        zoom: 10
                    }, {
                        searchControlProvider: 'yandex#search'
                    });
                
                for (var i in current_city.pickpoint) {
                    coord = current_city.pickpoint[i].coordinate.split(',');
                    myMap.geoObjects.add(new ymaps.Placemark([coord[0], coord[1]], {
                        //balloonContentHeader: "",
                        balloonContentBody: '<div class="baloon-poup" data-id="'+i+'">'+current_city.pickpoint[i].desc+'<br><p>Заберу от сюда</p></div>',
                        hintContent: current_city.pickpoint[i].name
                    }, {
                        preset: 'islands#dotIcon',
                        iconColor: '#4686cc'
                    }));
                }
                                
                $("#popupPickUp ul.pickup-list li").on('click', function (e) {
                    e.preventDefault();
                    var coord = $(this).attr('data-content').split(',');
                    myMap.geoObjects.get($(this).index()).balloon.open();
                    myMap.setCenter([coord[0], coord[1]], 14, {checkZoomRange: true});
                    return false;
                });       
            }
        }
        else {
            $('#order-our_stories_id').val('');
            
            if ($(".cartGoods").find('.row').eq(0).hasClass('active')) {
                $(".cartGoods").find('.row').eq(0).removeClass('active');
            }
            
            $(".cartGoods").find('.row').eq(0).hide();
        }
    }
JS
                    );
                    ?>
                    <div class="clear"></div>
                    <div class="pickup-text"></div>

                    <div class="string addr <?= $class_address ?> address_delivery_yandex">
                        <div id="mapyandex"></div>
                    </div>

                    <div class="string addr <?= $class_address ?> address_delivery">
                        <?= $form->field($model, 'street', ['options' => ['class' => 'col second']]); ?>
                        <?= $form->field($model, 'home', ['options' => ['class' => 'col third']]); ?>
                        <?= $form->field($model, 'house', ['options' => ['class' => 'col fourth']]); ?>
                    </div>
                    <?php echo $form->field($model, 'coordinates_json_yandex', ['options' => ['id' => 'coordinates_json_yandex']])->hiddenInput(['value' => null])->label(false); ?>
                    <?php echo $form->field($model, 'delivery_yandex', ['options' => ['id' => 'delivery_yandex']])->hiddenInput(['value' => null])->label(false); ?>
                    <?php echo $form->field($model, 'pickpoint_id', ['options' => ['id' => 'pickpoint_id']])->hiddenInput(['value' => null])->label(false); ?>
                <? else: ?>
                    <?
                    if ($context->city_model && $context->city_model->pickup) {
                        $text_pickup = $context->city_model->pickup;
                    } else {
                        $text_pickup = $context->settings->get('delivery_text_no_delivery');
                    }
                    ?>
                    <div class="string text">
                        <?= $text_pickup ?>
                    </div>
                <? endif; ?>
                <?= $form->field($model, 'comments', ['options' => ['class' => 'string']])->textarea(); ?>
                <!--<div class="string time-block<?php if ($cityId != 1): ?> hidden<?php endif; ?>">-->
                <!--<div class="string time-block hidden">-->
                <div class="hidden">
                    <?= $form->field($model, 'time_order', [
                        'template' => '{label}<div class="blSelect">{input}</div>'
                    ])->dropDownList($model->time_days); ?>
                </div>
            </div><!-- /delivery-info-block -->
            <div class="altTitle">Ваши данные</div>
            <? if (!$user): ?>
                <div class="string twoCol">
                    <?= $form->field($model, 'first_name'); ?>
                    <?= $form->field($model, 'last_name'); ?>
                </div>
            <? endif ?>
            <div class="string twoCol">
                <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(), [
                    'mask' => '+7(999)-999-9999',
                    'definitions' => [
                        'maskSymbol' => '_'
                    ],
                    'options' => [
                        'class' => ''
                    ]
                ]); ?>
            </div>
            <? if (!$user || ($user && !$user->email)): ?>
                <div class="string twoCol">
                    <?= $form->field($model, 'email'); ?>
                </div>
            <? endif ?>
        </div>
    </div>
    <div class="cartRight" id="cart_right">
        <div class="wrapperFixedPosition" id="wrap_fixed">
            <div class="cartOrder">
                <div class="cartLines">
                    <div class="cartLine">
                        <div class="string promoCode" id="check_promo">
                            <label>Промо-код, если есть</label>
                            <input type="text" name="code">
                            <a href="#" class="btn_Form blue button_check_promo">ОК</a>
                        </div>
                    </div>
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
                    <div class="cartLine">
                        <span>Доставка</span>
                        <div id="delivery_" class="cL_right delivery_price"><b>
                                <?php
                                if ($delivery > 0) {
                                    echo '<b>' . number_format($delivery, 0, '', ' ') . ' т.</b>';
                                } elseif ($delivery == 0) {
                                    echo '<i class="free">Бесплатная</i>';
                                } else {
                                    echo 'Только самовывоз или яндекс доставка';
                                }
                                ?>
                            </b></div>
                        <div id="delivery_clone"></div>
                    </div>
                    <div class="cartLine">
                        <span>Всего</span>
                        <div class="cL_right"><b class="basket_sum_full">
                                <?= number_format($sum_full, 0, '', ' ') ?> т.
                            </b></div>
                    </div>

                    <div class="cartLine wrap-min-order-sum" style="display:none;">
                        <span class="min-order-sum-city-label">Минимальная сумма заказа<br>для выбранного города</span>
                        <div class="cL_right min-order-sum-city"><b>0</b><b> т.</b></div>
                    </div>

                </div>
                <? if (!Yii::$app->user->isGuest && $user->bonus): ?>
                    <div class="string cCheckbox">
                        <?= Html::checkbox(Html::getInputName($model, 'bonus'), $model->bonus, ['id' => Html::getInputId($model, 'bonus'), 'uncheck' => 0]) ?>
                        <label for="<?= Html::getInputId($model, 'bonus') ?>">Использовать накопленные бонусы для
                            оплаты</label>
                    </div>
                <? endif ?>
                <div class="cartOrderControl">
                    <!--                    <button class="btn_Form blue" type="submit">Все верно, оплатить заказ</button>-->
                    <?= Html::button('Все верно, оплатить заказ', ['class' => 'btn_Form blue send']) ?>
                </div>
                <? d::res() ?>
            </div>
            <? if (!Yii::$app->user->isGuest): ?>
                <div class="cartBalance">
                    <div class="sometext">
                        <?
                        $percent_bonus = $context->function_system->percent();
                        $add_bonus = floor(((int) $sum * ($percent_bonus)) / 100)
                            ?>
                        <p><b>У вас
                                <?= Yii::$app->user->identity->bonus ?> бонусов
                            </b></p>
                        <p>Мы начислим вам <b>
                                <?= $add_bonus ?>
                            </b> бонусов за этот заказ</p>
                    </div>
                    <br>
                    <?
                    $all_bonus = BonusSettings::all();
                    $start_bonus = true;
                    $text_bonus = '';
                    $number = 0;
                    foreach ($all_bonus as $key_bon => $all_bon) {
                        if ($all_bon->price_start <= $user->order_sum && $all_bon->price_end >= $user->order_sum) {
                            if ($start_bonus) {
                                $text_bonus = 'У вас нет постоянной скидки. <br>';
                            } else {
                                $text_bonus = 'У вас ' . $all_bon->percent . '% постоянной скидки. <br>';
                            }
                            if (isset($all_bonus[$key_bon + 1])) {
                                $number = number_format($all_bonus[$key_bon + 1]->price_start - $user->order_sum, 0, '', ' ');
                                $text_bonus .= <<<HTML
Для получения скидки Вам осталось
                            сделать заказов на сумму <b>{$number} т.</b>
HTML;
                            }
                            break;
                        }
                        $start_bonus = false;
                    }
                    ?>
                    <? if ($text_bonus): ?>
                        <p>
                            <?php // echo $text_bonus; ?>
                        </p>
                    <? endif ?>
                </div>
            <? endif ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php

$coordinates = explode(',', '76.91835764257804,43.24041321668576');
$url_api_assessmentyandex = Url::to(['api/assessmentyandex']);

$url_check_promo = Json::encode(Url::to(['api/cart']));
//$this->registerJsFile('https://widget.cloudpayments.kz/bundles/cloudpayments');
$this->registerJs(<<<JS

//$('.address_delivery').addClass('hidden');
                  
$('.select-city span').on('click', function(){
    $('#popupPickUp').find('.popupDescription').remove();
});

/**
 * Для формы, событие beforeSubmit почему то не срабатывает.
 * Решил сделать через прослойку.
 */
$('#{$form_name} button.send').on('click', function(){
    var yandex_point = $('.pickup-text'),
//    pickup_radio = $('#{$form_name} .pickup-radio input[name="{$form_name}[delivery]"]:checked');
        delivery = $('#{$form_name}').serializeArray(),
        modal = $('#popupPickUp'),
        stop = deliveryMethod = yandexDeliveryEmptyAddress = false,
        delivery_clone = $('#delivery_clone');
    
    delivery.forEach(function(item, index, arr){
        if(item.name == '{$form_name}[delivery]'){
            if(item.value == '1'){
                stop = true;
            }
            if(item.value == '3'){
                // cl(delivery_clone.html().length);
                if(delivery_clone.html().length == 0){
                    yandexDeliveryEmptyAddress = true;
                }
            }
            deliveryMethod = true;
        }
    });

    // Не выбран способ доставки
    if(!deliveryMethod){
        $.growl.error({title: 'Внимание', message: 'Выберите способ доставки', duration: 5000});
        return;
    }

    // Если выбрана yandex доставка, то не выбран адрес доставки.
    if(yandexDeliveryEmptyAddress){
        $.growl.error({title: 'Внимание', message: 'Выберите адрес yandex доставки', duration: 5000});
        return;
    }
    
    // Если выбран самовывоз
    if(stop && $('.popup-block').is(':visible')){
        modal.find('.popupDescription').remove();
        // Если не выбран адрес для самовывоза.
        if(yandex_point.html() == ''){
            jQuery('<div>', {
                class: 'popupDescription',
            }).prependTo(modal.find('.popup'));
            setTimeout(function(){
                modal.find('.popupDescription').html('Пожалуйста<br>укажите пункт самовывоза');
                modal.popup('open');
            }, 200);
            return;
        }
    }
    
    $('#{$form_name}').submit();
});
                  
$('.button_check_promo').on('click', function (e) {
    e.preventDefault();
	var res = $('.res');
    var element = $('input', '#check_promo');
    res.html('result');
    if ($.trim(element.val()) == '') {
        $('#check_promo').addClass('error');
        if (!$(element).data('tooltipster-ns')) {
            $(element).tooltipster({
                content: 'Поле не заполнено!'
            });
        } else {
            $(element).tooltipster('content', 'Поле не заполнено!');
            $(element).tooltipster('enable');
        }
    } else {
        var request_data = $('input', '#check_promo').serializeArray();
        var add_data = {name: 'action', value: 'check_promo'};
        request_data.push(add_data);
        request_data.push({name: 'city', value: {$context->city}});
        $('#check_promo').removeClass('error');
        if ($(element).data('tooltipster-ns')) {
            $(element).tooltipster('disable');
        }
        $('#loader').show();
        $.ajax({
            url: {$url_check_promo},
            type: 'GET',
            dataType: 'JSON',
            data: request_data,
            success: function (data_return) {
				// res.html('<pre>' + prettyPrintJson.toHtml(data_return) + '</pre>');
                $('#loader').hide();
                if (typeof data_return.errors != 'undefined') {
                    $('#check_promo').addClass('error');
                    if (!$(element).data('tooltipster-ns')) {
                        $(element).tooltipster({
                            content: data_return.errors
                        });
                    } else {
                        $(element).tooltipster('content', data_return.errors);
                        $(element).tooltipster('enable');
                    }
                } else {
                    $('.basket_sum').text(data_return.sum);
                    $('.delivery_price').html(data_return.delivery);

                    $('.basket_sum_full').text(data_return.sum_full);
                    if (typeof data_return.discount_price != 'undefined') {
                        $('.basket_sum_discount').html(data_return.discount_price);
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
            error: function (data_return) {
				res.html(JSON.stringify(data_return));
                $('#loader').hide();
                $.growl.error({title: 'Ошибка', message: "Произошла ошибка на стороне сервера!", duration: 5000});
            }
        });
    }
})
$("#{$form_name}-city").chosen({disable_search_threshold: 10});
$("#{$form_name}-address_id").chosen({disable_search_threshold: 10});
$("#{$form_name}-time_order").chosen({disable_search_threshold: 10});
$("#{$form_name}-payment").chosen({disable_search_threshold: 10});
cart_fixed_block();
$(document).on('click', "#popupPickUp .baloon-poup", function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    $('#order-our_stories_id').val(id);
    $('#order-type_delivery').val(0);
    $('.pickup-text').html('Выбран пункт - ' + $('#popupPickUp .pickup-list li[data-id=' + id + ']').html());
    $('#pick-up').prop('checked', 'checked');
    $('#popupPickUp').popup('close');
});

$(document).on('click', "#cart_list .cartGoods .row a", function (e) {
    e.preventDefault();
    window.open(e.target.href, '_blank');
});

//ymaps.ready(init_yandex);

if('{$js_yandex_click}' == '1'){
    setTimeout(function(){
        $('#yandex_delivery').trigger('click');
    }, 500);
}

// if('{$js_pickup_click}' == '1'){
//     setTimeout(function(){
//         $('.pickup-radio').trigger('click');
//     }, 1500);
// }
                  
function init_yandex() {   

    var myMap = new ymaps.Map('mapyandex', {
            center: [{$coordinates[0]}, {$coordinates[1]}],
            zoom: 12,
            controls: []
        }),  

        routePanelControl = new ymaps.control.RoutePanel({
            options: {
                // Добавим заголовок панели.
                showHeader: true,
				maxWidth: '410px', 
                title: 'Расчёт доставки'
            }
        }),
        zoomControl = new ymaps.control.ZoomControl({
            options: {
                size: 'small',
                float: 'none',
                position: {
                    bottom: 145,
                    right: 10
                }
            }
        });
		// Пользователь сможет построить только автомобильный маршрут.
		routePanelControl.routePanel.options.set({
			types: {auto: true}
		});

		// Если вы хотите задать неизменяемую точку "откуда", раскомментируйте код ниже.
		routePanelControl.routePanel.state.set({
			fromEnabled: false,
			from: 'проспект Сакена Сейфуллина, 617'
		 });   

		myMap.controls.add(routePanelControl).add(zoomControl);

		routePanelControl.routePanel.getRouteAsync().then(function (route) {
 
        route.model.setParams({results: 1}, true);

        route.model.events.add('requestsuccess', function () {
	
			var activeRoute = route.getActiveRoute();  
				  
			if (activeRoute) {
								
				var jsonString = JSON.parse(JSON.stringify(activeRoute.properties._data.boundedBy));
				$('#order-coordinates_json_yandex').attr('value',jsonString);
					
				var name = $('.ymaps-2-1-78-route-panel-input__input[placeholder*=Куда]').val();
					
				var request_data = {
					'longitude_from':'76.935404',
					'latitude_from':'43.233781',
					'longitude_to':activeRoute.properties._data.boundedBy[1][1],
					'latitude_to':activeRoute.properties._data.boundedBy[1][0],
					'name': name
				};
						
				$('#loader').show();
				$.ajax({
					url: '{$url_api_assessmentyandex}',
					type: 'POST',
					dataType: 'JSON',
					data: request_data,
					success: function (data_return) {
						if (!data_return.code) {
							$.growl.error({title: 'Ошибка', message: "Выберите другое назначение", duration: 5000});
							$('#loader').hide();
						} else {
							if (!data_return.coord_answer) {
								$.growl.error({title: 'Ошибка', message: "Выберите другое назначение", duration: 5000});
								$('#loader').hide();
							}
							
							$('#order-coordinates_json_yandex').attr('value',data_return.coord_answer);
							
							$('#loader').hide();
							$('#delivery_').html('<b>' + data_return.code + ' т.</b>');
							$('#order-delivery_yandex').val(data_return.code);
							$('#delivery_clone').html(data_return.code);

							var sum_yandex = Number({$sum}) + Number(data_return.code);

							$('#cart_right .cartLine:nth-child(5) .basket_sum_full').html('' + sum_yandex+ ' т.');

							$('#pr_delivery').html('<b>Стоимость: ' + data_return.code + '</b>');
							$.growl.notice({title: 'Успех', message: 'Стоимость яндекс доставки: ' + data_return.code, duration: 5000});

							var name = $('.ymaps-2-1-78-route-panel-input__input[placeholder*=Куда]').val();

							$('#order-street').prop('readonly', false);

							$('#order-street').attr('value', '');
							$('#order-home').attr('value', '');
							$('#order-house').attr('value','');
							var address = name.split(",");
							$('#order-street').val((address[0] != 'undefined') ? address[0] : '');
							$('#order-home').val((address[1] != 'undefined') ? address[1] : '');
							$('#order-house').val((address[2] != 'undefined') ? address[2] : '');
							$('#order-street').prop('readonly', true);
							$('#order-home').prop('readonly', true);							
						}					
					},
					error: function () {
					$('#loader').hide();
					
					}
				}); 
     
				// Получим протяженность маршрута.
				var length = route.getActiveRoute().properties.get("distance"),

				// Создадим макет содержимого балуна маршрута.
				balloonContentLayout = ymaps.templateLayoutFactory.createClass(
				'<span>Расстояние: ' + length.text + '.</span><br/>' +
				'<span id="pr_delivery" style="font-weight: bold; min-width:400px; font-style: italic">Стоимость: 0 т.</span>');
				// Зададим этот макет для содержимого балуна.
				route.options.set('routeBalloonContentLayout', balloonContentLayout);
				// Откроем балун.
			activeRoute.balloon.open(); }
		});
    });
}


ymaps.ready(init_);

function init_() {

	  var myMap = new ymaps.Map('mapyandex', {
             center: [43.224729,76.93376],
            zoom: 12,
            controls: ['geolocationControl', 'searchControl']
        }),
        deliveryPoint = new ymaps.GeoObject({
            geometry: {type: 'Point'},
            properties: {iconCaption: 'Адрес'}
        }, {
            preset: 'islands#blackDotIconWithCaption',
            draggable: false,
            iconCaptionMaxWidth: '255'    
        }),
        searchControl = myMap.controls.get('searchControl');
		
	//	map.controls.add(searchControl, { left: '40px', top: '10px' });
		
	//	searchControl.add({ left: '40px', top: '10px' });
		
	//	var myIconContentLayout = ymaps.templateLayoutFactory.createClass('<input type="text" name="name" class="square_layout"></div>');
		
	//	  searchControl.options.set('size', 'large');
	
		searchControl.options.set({
		//	provider: 'yandex#search',
	//	layout:myIconContentLayout, 
		//	height: 15,
			noPlacemark: true,
			placeholderContent: 'Введите адрес доставки',
            fitMaxWidth: true,
            size: 'large',
		//	size: 'small',
	
	//		maxWidth: [1700, 1900, 220]
			  maxWidth: [10, 20, 200]

		});

    myMap.geoObjects.add(deliveryPoint);

    function onZonesLoad(json) {
        // Добавляем зоны на карту.
        var deliveryZones = ymaps.geoQuery(json).addToMap(myMap);
        // Задаём цвет и контент балунов полигонов.
        deliveryZones.each(function (obj) {
            obj.options.set({
                fillColor: obj.properties.get('fill'),
                fillOpacity: obj.properties.get('fill-opacity'),
                strokeColor: obj.properties.get('stroke'),
                strokeWidth: obj.properties.get('stroke-width'),
                strokeOpacity: obj.properties.get('stroke-opacity')
            });
            obj.properties.set('balloonContent', obj.properties.get('description'));
        });

        // Проверим попадание результата поиска в одну из зон доставки.
        searchControl.events.add('resultshow', function (e) {
            highlightResult(searchControl.getResultsArray()[e.get('index')]);
        });

        // Проверим попадание метки геолокации в одну из зон доставки.
        myMap.controls.get('geolocationControl').events.add('locationchange', function (e) {
            highlightResult(e.get('geoObjects').get(0));
        });

        // При перемещении метки сбрасываем подпись, содержимое балуна и перекрашиваем метку.
        deliveryPoint.events.add('dragstart', function () {
            deliveryPoint.properties.set({iconCaption: '', balloonContent: ''});
            deliveryPoint.options.set('iconColor', 'black');
        });

        // По окончании перемещения метки вызываем функцию выделения зоны доставки.
        deliveryPoint.events.add('dragend', function () {
            highlightResult(deliveryPoint);
        });

        function highlightResult(obj) {
            // Сохраняем координаты переданного объекта.
            var coords = obj.geometry.getCoordinates(),
            // Находим полигон, в который входят переданные координаты.
                polygon = deliveryZones.searchContaining(coords).get(0);

            if (polygon) { 
						
			// console.log(polygon);						
			// console.log(polygon.properties._data.lat);
					
			// Уменьшаем прозрачность всех полигонов, кроме того, в который входят переданные координаты.
			deliveryZones.setOptions('fillOpacity', 0.4);
			polygon.options.set('fillOpacity', 0.8);
			// Перемещаем метку с подписью в переданные координаты и перекрашиваем её в цвет полигона.
			deliveryPoint.geometry.setCoordinates(coords);
			deliveryPoint.options.set('iconColor', polygon.properties.get('fill'));
			
				// Задаем подпись для метки.
				if (typeof(obj.getThoroughfare) === 'function') {
					// console.log(obj.geometry._coordinates[0]);
					
					setData(obj);
						
					var address = [obj.getThoroughfare(), obj.getPremiseNumber(), obj.getPremise()].join(',').slice(0,-1);
					
					//  var address = [obj.getThoroughfare(), obj.getPremiseNumber(), obj.getPremise()].join(' ');
					if (address.trim().length < 5) {
						address_ = obj.getAddressLine().split(",");
						address = address_[2] + ',' + address_[3]; 									
					}	
//					var ll =
//						'longitude_from- ' + polygon.properties._data.lat + '\\n'
//						+'latitude_from- ' + polygon.properties._data.lon + '\\n'
//						+'longitude_to- ' + obj.geometry._coordinates[1] + '\\n'
//						+'latitude_to- ' + obj.geometry._coordinates[0];
//					console.log(ll);
					
				 	var request_data = {
						'longitude_from': polygon.properties._data.lat,
						'longitude_to': obj.geometry._coordinates[1],
						'latitude_from': polygon.properties._data.lon,
						'latitude_to': obj.geometry._coordinates[0],
						'name': address,
						'pick': polygon.properties._data.pick
					};
					
					$('#loader').show();
					var res = $('.res');
					$.ajax({
						url: '{$url_api_assessmentyandex}',
						type: 'POST',
						dataType: 'JSON',
						data: request_data,
						success: function (data_return) {
							res.html('<pre>' + prettyPrintJson.toHtml(data_return) + '</pre>');
							if (!data_return.code) {
								$.growl.error({title: 'Ошибка', message: "Выберите другое назначение", duration: 5000});
								$('#loader').hide();
							} else {
								if (!data_return.coord_answer) {
									$.growl.error({title: 'Ошибка', message: "Выберите другое назначение", duration: 5000});
									$('#loader').hide();
								}
								
								$('#order-coordinates_json_yandex').attr('value',data_return.coord_answer);
								$('#order-pickpoint_id').attr('value', data_return.pick);
																			
								$('#loader').hide();
								$('#delivery_').html('<b>' + number_format(data_return.code, 0, '', '') + ' т.</b>');
								$('#order-delivery_yandex').val(number_format(data_return.code, 0, '', ''));
								$('#delivery_clone').html(number_format(data_return.code, 0, '', ''));

								var sum_yandex = Number({$sum}) + Number(data_return.code);

								$('#cart_right .cartLine:nth-child(5) .basket_sum_full').html('' + number_format(sum_yandex, 0, '', '') + ' т.');

								$('#pr_delivery').html('<b>Стоимость: ' + number_format(data_return.code, 0, '', '') + '</b>');
								$.growl.notice({title: 'Успех', message: 'Стоимость яндекс доставки: ' + number_format(data_return.code, 0, '', ''), duration: 5000});

								// var name = $('.ymaps-2-1-78-route-panel-input__input[placeholder*=Куда]').val();

								$('#order-street').prop('readonly', false);

								$('#order-street').attr('value', '');
								$('#order-home').attr('value', '');
								$('#order-house').attr('value','');
								var name = data_return.name;
							    // $('#orders-user_address').val(data_return.name);
								var address = name.split(",");
								$('#order-street').val((address[0] != 'undefined') ? address[0] : '');
								$('#order-home').val((address[1] != 'undefined') ? address[1] : '');
								$('#order-house').val((address[2] != 'undefined') ? address[2] : '');
								$('#order-street').prop('readonly', true);
								$('#order-home').prop('readonly', true);							
							}					
						},
						error: function () {
						  $('#loader').hide();
						}
					}); 		
                } else {									
                    // Если вы не хотите, чтобы при каждом перемещении метки отправлялся запрос к геокодеру,
                    // закомментируйте код ниже.
                    ymaps.geocode(coords, {results: 1}).then(function (res) {
                        var obj = res.geoObjects.get(0);						
                        setData(obj);
                    });
                }
            } else {
                // Если переданные координаты не попадают в полигон, то задаём стандартную прозрачность полигонов.
                deliveryZones.setOptions('fillOpacity', 0.4);
                // Перемещаем метку по переданным координатам.
                deliveryPoint.geometry.setCoordinates(coords);
                // Задаём контент балуна и метки.
                deliveryPoint.properties.set({
                    iconCaption: 'Доставка не осуществляется в этот пункт',
                    balloonContent: 'Cвяжитесь с оператором',
                    balloonContentHeader: ''
                });
                // Перекрашиваем метку в чёрный цвет.
                deliveryPoint.options.set('iconColor', 'black');
            }

            function setData(obj){
                var address = [obj.getThoroughfare(), obj.getPremiseNumber(), obj.getPremise()].join(' ');
                if (address.trim() === '') {
                    address = obj.getAddressLine();
                }
                var price = polygon.properties.get('description');
                price = price.match(/<strong>(.+)<\/strong>/)[1];
                deliveryPoint.properties.set({
                    iconCaption: address,
                    balloonContent: address,
                    balloonContentHeader: price
                });
            }
        }
    }
 
    $.ajax({
        url: '../../uploads/data.geojson',
        dataType: 'json',
        success: onZonesLoad
    });
}
                  
function yandexDeliveryMap(){
    // 	$('.ymaps-2-1-78-float-button.ymaps-2-1-78-_hidden-icon').trigger('click');				   

    $('.address_delivery_yandex').closest('.string').removeClass('hidden');

    $('.address_delivery').addClass('hidden');

    $('#mapyandex').css('display','block');

    if ($('#order-address_id').length == 0) {
        if ($('.address_delivery').hasClass('hidden')) {
            $('.address_delivery').removeClass('hidden');
        }
    }else {
        if (!$('.address_delivery').hasClass('hidden')) {
            $('.address_delivery').addClass('hidden');
        }
    } 

    var name = $('.ymaps-2-1-78-route-panel-input__input[placeholder*=Куда]').val();

    $('#order-street').prop('readonly', false);
    $('#order-street').val('');
    $('#order-home').val('');
    $('#order-house').val('');

    if (name) {
        var address = name.split(",");
        $('#order-street').val((address[0] != 'undefined') ? address[0] : '');
        $('#order-home').val((address[1] != 'undefined') ? address[1] : '');
        $('#order-house').val((address[2] != 'undefined') ? address[2] : '');
    }
    $('#order-street').prop('readonly', true);
    $('#order-home').prop('readonly', true);
}
                  
function setDeliveryMethod(obj){
    var input = $(obj).find('input');
    $('#cart_right .cartOrderControl button[type=submit]').removeAttr('disabled');

    if (input.prop('checked') == true || input.prop('disabled') == true) {
        //input.prop('checked', false);
        // console.log('d23');
    }
    else {
        input.prop('checked', true);
        var current_city = city_pickup[$("#{$form_name}-city").val()];
        changeAddress(input.val());

        $('#order-type_delivery').val(input.val());
        $(obj).closest('.cartGoods').find('.row.active').removeClass('active');
        $(obj).addClass('active');

        if (input.val() == 1) {

            cl('Самовывоз');

            if (current_city.pickup_price > 0) {
                $('#cart_right .cartLine:nth-child(4) .delivery_price b').html(current_city.pickup_price);
                $('#cart_right .cartLine:nth-child(5) .basket_sum_full').html(current_city.pickup_sum_all);
            }
            else {
                if (current_city.pickpoint !== null) {
                    for (var i in current_city.pickpoint) {
                        $('#cart_right .cartLine:nth-child(4) .delivery_price b').html(current_city.pickpoint[i].delivery);
                        $('#cart_right .cartLine:nth-child(5) .basket_sum_full').html(current_city.pickpoint[i].sum_all);

                        break;
                    }
                }
                else {
                    $('#cart_right .cartLine:nth-child(4) .delivery_price b').html('<i class="free">Самовывоз</i>');
                }
            }

        } else if (input.val() == 3) {
            cl('Yandex доставка');
            var delivery_clone = $('#delivery_clone').html();

            if (delivery_clone.length == 0) {
                $('#cart_right .cartLine:nth-child(4) .delivery_price b').html('<i class="free"><a class="free" href="#mapyandex">Введите адрес для расчета</a></i>');
            } else {
                $('#cart_right .cartLine:nth-child(4) .delivery_price b').html('<b>' + delivery_clone+ ' т.</b>');
            }

            var sum_yandex = Number({$sum}) + Number(delivery_clone);

            $('#cart_right .cartLine:nth-child(5) .basket_sum_full').html('' + sum_yandex+ ' т.');		

            $('#cart_right .cartLine:nth-child(5) .basket_sum_full').html(sum);


            $('.delivery-address').hide();

            $('.address_delivery').removeClass('hidden');

            //console.log('d3');

            //$('.ymaps-2-1-78-float-button.ymaps-2-1-78-_hidden-icon').trigger('click');		

        } else {
            cl('Доставка курьером');

            var delivery = current_city.pickup_price;
            var sum = current_city.pickup_sum_all;

            if (current_city.delivery_price > 0) {
                delivery = current_city.delivery;
                sum = current_city.sum_all;
            } else if (current_city.delivery_price == 0) {
                delivery = '<i class="free">'+current_city.delivery+'</i>';
                sum = current_city.sum_all;
            }

            $('#cart_right .cartLine:nth-child(4) .delivery_price b').html(delivery);
            $('#cart_right .cartLine:nth-child(5) .basket_sum_full').html(sum);
        }

        $('.pickup-text').html('');
        $('#order-our_stories_id').val('');
    }
}

JS
);
?>

<style>
    #delivery_clone {
        visibility: hidden;
    }

    #mapyandex {
        width: 100%;
        height: 350px;
        display: none;
    }

    .select-city {
        white-space: nowrap;
        margin: 6px 0 6px 20px;
        font-size: 1.3em;
        font-weight: 600;
        width: 133px;
    }

    .select-city:before {
        content: '\2714';
        color: #4686cc;
        margin-right: .5rem;
    }

    .select-city span {
        cursor: pointer;
        color: #4686cc;
        font: bold 1.2em/1.5em "Proxima Nova", sans-serif;
        border-bottom: 1px dotted #c7d7ed;
    }

    .popup-header {
        font-size: 2em;
        margin: 30px 0 20px 0;
    }

    #popupPickUp ul li {
        margin-bottom: 15px;
        color: #4686cc;
        font: bold 1.5em "Proxima Nova", sans-serif;
        text-decoration: dotted;
        cursor: pointer;
    }

    #popupPickUp .baloon-poup {
        max-width: 350px;
    }

    #popupPickUp .baloon-poup p {
        background: #4686cc;
        padding: 5px 10px;
        color: #ffffff;
        text-align: center;
        margin: 10px 0;
        cursor: pointer;
    }

    .cartGoods input[type=radio]:checked+label {
        font-weight: 600;
        color: #4686cc;
    }

    .cartGoods div.row {
        display: block;
        border: 1px solid #a6cbf5;
        border-radius: 4px;
        padding: 13px;
        margin-bottom: 10px;
        width: 30%;
        float: left;
        margin-right: 20px;
        height: 95px;
    }

    .cartGoods div.row.active {
        background-color: #e8f0f9;
    }

    .cartGoods div.row.disabled {
        cursor: default;
        background-color: #ececec;
        color: #808080;
    }

    .cartGoods div.row.disabled label {
        cursor: default;
        color: #808080;
    }

    .cartGoods .row label+p {
        margin-top: 10px;
        font: 1.3em "Proxima Nova", sans-serif;
    }

    .pickup-text {
        font: 1.6em/1.1em "Proxima Nova", sans-serif;
        margin-bottom: 10px;
    }

    .clear {
        width: 100%;
    }

    .cartGoods .delivery-address {
        float: left;
    }

    @media (max-width: 999px) {
        .overlayWinmod #popupPickUp.popup.window {
            max-width: 93% !important;
        }
    }

    @media (max-width: 800px) {
        .cartGoods div.row {
            float: none;
            width: 100%;
        }
    }
</style>