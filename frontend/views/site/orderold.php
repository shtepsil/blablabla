<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $items Items[]
 * @var $sets Sets[]
 * @var $a_address \common\models\UserAddress[]
 */

use common\models\BonusSettings;
use common\models\City;
use common\models\Items;
use common\models\Orders;
use common\models\Sets;
use common\models\UserAddress;
use frontend\form\Order;
use frontend\widgets\ActiveField;
use frontend\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

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
        $data_address = ArrayHelper::map($a_address, function ($el) {
            return $el->id;
        }, function ($el) use (&$data_phones, &$city, &$start) {
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
?>
<script src="https://api-maps.yandex.ru/2.1/?apikey=a800cece-8fcc-4d1c-bc91-da2061eb8d3e&lang=ru_RU" type="text/javascript"></script>
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
                            <span>Арт. <?= $item->article ?></span>
                        <? endif ?>
                    </a>
                    <div class="cG_center">
                        <a class="title" href="<?= $item->url() ?>" target="_blank"><?= $item->name ?></a>
                        <? if ($item->body_small): ?>
                            <div class="descript"><?= $item->body_small ?></div>
                        <? endif ?>
                        <? if ($item->weight): ?>
                            <div class="weight"><?= $item->weight ?> кг.</div>
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
                            <?= $count ?> <?= ($item->measure) ? 'шт' : 'кг' ?>.
                        </div>
                        <div class="price"><?= number_format($item_sum, 0, '', ' ') ?> т.</div>
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
                        <a href="<?= Url::to(['site/set', 'id' => $item->id]) ?>"
                           class="title"><?= $item->name ?></a>
                    </div>
                    <div class="cG_right">
                        <div class="numSize">
                            <?= $count ?> шт.
                        </div>
                        <div class="price"><?= number_format($item_sum, 0, '', ' ') ?> т.</div>
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
                <?php if (!empty($paymentType)):?>
                    <?= $form->field($model, 'payment', [
                        'template' => '{label}<div class="blSelect payment">{input}</div>'
                    ])->dropDownList($paymentType); ?>
                <?php else:?>
                    нет доступных способов оплаты
                <?php endif;?>
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
        <div class="cartGoods">
            <?= Html::activeHiddenInput($model, 'type_delivery') ?>
            <?= Html::activeHiddenInput($model, 'only_pickup') ?>
            <div class="string select_address">
                <?= $form->field($model, 'city', [
                    'template' => '{label}<div class="blSelect">{input}</div>'
                ])->dropDownList($context->function_system->data_city); ?>
            </div>
            <? if ($model->type_delivery == 1): ?>
                <?php $info = Json::decode($cityInfo);
                $info = $info[$cityId];
                $pickpoints = ($info ? $info['pickpoint'] : null); ?>
                <div class="row<?=($info['delivery_price'] !== 0 ? ' active' : '')?>">
                    <div class="col-md-1">
                        <?=Html::radio($form_name.'[delivery]', (!empty($pickpoints) && $info['delivery_price'] !== 0 ? true : false), [
                            'value' => 1,
                            'autocomplete' => 'off',
                            'id' => 'pick-up'
                        ])?>
                        <?=Html::label('Самовывоз')?>
                        <?php
                        if (!empty($pickpoints)) {
                            $coord = $info['coordinate'];
                            $this->registerJs(<<<JS
    ymaps.ready(init);
    
    function init() {
        var myMap = new ymaps.Map("map", {
                center: [$coord],
                zoom: 10
            }, {
                searchControlProvider: 'yandex#search'
            });
JS
                            ); ?>
                            <div class="popup-block">
                                <div class="select-city" onclick="popup({block_id: '#popupPickUp', action: 'open'});">
                                    <span>Выбрать пункт</span>
                                </div>
                                <div class="overlayWinmod">
                                    <div id="popupPickUp" class="popup window">
                                        <div class="popupClose" onclick="popup({block_id: '#popupPickUp', action: 'close'});"></div>
                                        <p class="popup-header">Выбрать пункт</p>
                                        <ul class="pickup-list">
                                            <?php foreach ($pickpoints as $key => $pickpoint) :?>
                                                <?php
                                                $coord = $pickpoint['coordinate'];
                                                $name = $pickpoint['name'];
                                                $address = $pickpoint['desc'];?>
                                                <li data-id="<?=$pickpoint['id']?>" data-content="<?=$coord?>">
                                                    <?=$name?>
                                                </li>
                                                <?php
                                                $content = '<div class="baloon-poup" data-id="'.$pickpoint['id'].'">'.$address.'<br><p>Заберу от сюда</p></div>';
                                                $this->registerJs(<<<JS
            myMap.geoObjects.add(new ymaps.Placemark([$coord], {
                //balloonContentHeader: "$name",
                balloonContentBody: '$content',
                hintContent: "$name"
            }, {
                preset: 'islands#dotIcon',
                iconColor: '#4686cc'
            }));
JS
                                                );
                                                ?>
                                            <?php endforeach;?>
                                        </ul>
                                        <?php
                                        $this->registerJs(<<<JS
            $("#popupPickUp ul.pickup-list li").on('click', function (e) {
                e.preventDefault();
                var coord = $(this).attr('data-content').split(',');
                myMap.geoObjects.get($(this).index()).balloon.open();
                myMap.setCenter([coord[0], coord[1]], 14, {checkZoomRange: true});
                
                return false;
            });

}
JS
                                        ); ?>
                                        <div id="map"></div>
                                    </div>
                                </div>
                            </div>
                        <?php }?>
                    </div>
                </div>
                <?php
                $class = '';
                $style = '';
                $checked = false;
                $disabled = false;
                $text = $info['text_courier'];
                if ($info['only_pickup']) {
                    $style = 'display:none';
                }
                elseif ($info['courier_price'] > 0 && $sum < $info['courier_price']) {
                    $class = ' disabled';
                    $disabled = true;
                    $text = $info['courier_price_text'];
                }
                elseif ($info['delivery_price'] === 0 || empty($pickpoints)) {
                    $class = ' active';
                    $checked = true;
                }
                ?>
                <div class="row<?=$class?>" style="<?=$style?>">
                    <div class="col-md-1">
                        <?=Html::radio($form_name.'[delivery]', $checked, [
                            'value' => 2,
                            'autocomplete' => 'off',
                            'disabled' => $disabled
                        ])?>
                        <?=Html::label('Курьером до двери')?>
                        <p><?=$text?></p>
                    </div>
                </div>
                <?php if ($pickpoints) {
                    $currentPickpoint = current($pickpoints);
                } ?>
                <?= Html::hiddenInput($form_name.'[our_stories_id]', ($pickpoints ? $currentPickpoint['id'] : ''), [
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
var city_pickup = {$cityInfo};

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
            $('.delivery-address').hide();
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
        $('<span style="color:red;margin:10px 0;margin-top:-10px;font-size:16px;display:inline-block;font-family:Proxima Nova;">В воскресение доставка курьером не работает. Ваш заказ будет доставлен в понедельник.</span><br/>').prependTo($('.select_address')[1]);
}

if ($("#{$form_name}-city").is(':visible')) {
    only_pickup();
} else {
    check_address_user($("#{$form_name}-address_id").val());
}

$("#{$form_name}-city").on('change', function (e) {
    var current_city = city_pickup[$(this).val()];
    
    $('#cart_right .cartOrderControl button[type=submit]').removeAttr('disabled');
    
    if ($(".cartGoods").find('.row').eq(0).hasClass('disabled')) {
        $(".cartGoods").find('.row').eq(0).removeClass('disabled');
        $(".cartGoods").find('.row').eq(0).find('input').removeAttr('disabled');
    }
    
    if ($(".cartGoods").find('.row').eq(1).hasClass('disabled')) {
        $(".cartGoods").find('.row').eq(1).removeClass('disabled');
        $(".cartGoods").find('.row').eq(1).find('input').removeAttr('disabled');
    }
    
    $("#order-payment option").remove();
    
    for (var i in current_city.payment) {
        $("#order-payment")
         .append($("<option></option>")
                    .attr("value", i)
                    .text(current_city.payment[i])
         );
    }

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
    
    if (current_city.pickpoint !== null) {
        $(".cartGoods").find('.row').eq(0).find('input').prop('checked', true);
        $(".cartGoods").find('.row').eq(0).show();
        $(".cartGoods").find('.row').eq(1).hide();
        $(".cartGoods").find('.row').eq(0).find('.popup-block').show();
        changeDeliveries(current_city);
        
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
        
        $('#order-type_delivery').val(0);
    }
    else {
        $('#order-our_stories_id').val('');
        $(".cartGoods").find('.row').eq(0).find('.popup-block').hide();
        
        var delivery = current_city.pickup_price;
        var sum = current_city.pickup_sum_all;
        
        if (current_city.only_pickup) {
            $(".cartGoods").find('.row').eq(0).find('input').prop('checked', true);
            $('#cart_right .cartOrderControl button[type=submit]').prop('disabled', true);
            
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
            if (current_city.courier_price == 0) {
                $(".cartGoods").find('.row').eq(1).find('input').prop('checked', true);
            }
            else {
                $('#cart_right .cartOrderControl button[type=submit]').prop('disabled', true);
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
});

$(document).on('click', "#cart_list .cartGoods .row:not(.disabled)", function (e) {
    e.preventDefault();
    
    var input = $(this).find('input');
    $('#cart_right .cartOrderControl button[type=submit]').removeAttr('disabled');
    
    if (input.prop('checked') == true || input.prop('disabled') == true) {
        //input.prop('checked', false);
    }
    else {
        input.prop('checked', true);
        var current_city = city_pickup[$("#{$form_name}-city").val()];
        changeAddress(input.val());
        
        $('#order-type_delivery').val(input.val());
        $(this).closest('.cartGoods').find('.row.active').removeClass('active');
        $(this).addClass('active');
        
        if (input.val() == 1) {
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
                    $('#cart_right .cartLine:nth-child(5) .basket_sum_full').html(current_city.sum_all);
                }
            }
        }
        else {
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
    }
    else {
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
        
        $("#popupPickUp").html('');
        var html = '<div class="popupClose" onclick="popup({block_id: \'#popupPickUp\', action: \'close\'});"></div>'+
                                 '<p class="popup-header">Выбрать пункт</p>'+
                                  '<ul class="pickup-list">';
            
        for (var i in current_city.pickpoint) {
             if (k++ == 0) {
                var text = '<div class="baloon-poup" data-id="'+current_city.pickpoint[i].coordinate+'">'+current_city.pickpoint[i].name+'<br><p>Заберу от сюда</p></div>';
                $('#order-our_stories_id').val(i);
                
                $('#cart_right .cartLine:nth-child(4) .delivery_price b').html(current_city.pickpoint[i].delivery);
                $('#cart_right .cartLine:nth-child(5) .basket_sum_full').html(current_city.pickpoint[i].sum_all);
             }
                    
             html += '<li data-id="'+i+'" data-content="'+current_city.pickpoint[i].coordinate+'">' +
                        current_city.pickpoint[i].name+
                   '</li>';
        }
        
        html += '</ul><div id="map"></div>';
        
        if (current_city.delivery_price == 0) {
            if ($(".cartGoods").find('.row').eq(0).hasClass('active')) {
                $(".cartGoods").find('.row').eq(0).removeClass('active');
            }
            
            if (!$(".cartGoods").find('.row').eq(1).hasClass('active')) {
                $(".cartGoods").find('.row').eq(1).addClass('active');
            }
            
            $(".cartGoods").find('.row').eq(1).find('input').prop('checked', 'checked');
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
        
        $("#popupPickUp").append(html);
        
        ymaps.ready(init);
    
        function init() {
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
                <div class="string addr <?= $class_address ?> address_delivery">
                    <?= $form->field($model, 'street', ['options' => ['class' => 'col second']]); ?>
                    <?= $form->field($model, 'home', ['options' => ['class' => 'col third']]); ?>
                    <?= $form->field($model, 'house', ['options' => ['class' => 'col fourth']]); ?>
                </div>
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
            <div class="string time-block<?php if ($cityId != 1):?> hidden<?php endif;?>">
                <?= $form->field($model, 'time_order', [
                    'template' => '{label}<div class="blSelect">{input}</div>'
                ])->dropDownList($model->time_days); ?>
            </div>
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
                        <div class="cL_right"><b class="basket_sum"><?= number_format($sum + $discount_price, 0, '', ' ') ?> т.</b></div>
                    </div>
                    <div class="cartLine <?= ($discount_price) ? '' : 'hidden' ?>" id="discount_block">
                        <span>Скидка</span>
                        <div class="cL_right"><b class="basket_sum_discount"><?= number_format($discount_price, 0, '', ' ') ?> т.</b></div>
                    </div>
                    <div class="cartLine">
                        <span>Доставка</span>
                        <div class="cL_right delivery_price"><b><?php
                                if ($delivery > 0) {
                                    echo '<b>' . number_format($delivery, 0, '', ' ') . ' т.</b>';
                                }
                                elseif ($delivery == 0) {
                                    echo '<i class="free">Бесплатная</i>';
                                }
                                else {
                                    echo 'Только самовывоз';
                                }
                                ?></b></div>
                    </div>
                    <div class="cartLine">
                        <span>Всего</span>
                        <div class="cL_right"><b class="basket_sum_full"> <?= number_format($sum_full, 0, '', ' ') ?> т.</b></div>
                    </div>
                </div>
                <? if (!Yii::$app->user->isGuest && $user->bonus): ?>
                    <div class="string cCheckbox">
                        <?= Html::checkbox(Html::getInputName($model, 'bonus'), $model->bonus, ['id' => Html::getInputId($model, 'bonus'), 'uncheck' => 0]) ?>
                        <label for="<?= Html::getInputId($model, 'bonus') ?>">Использовать накопленные бонусы для оплаты</label>
                    </div>
                <? endif ?>
                <div class="cartOrderControl">
                    <button class="btn_Form blue" type="submit">Все верно, оплатить заказ</button>
                </div>
            </div>
            <? if (!Yii::$app->user->isGuest): ?>
                <div class="cartBalance">
                    <div class="sometext">
                        <?
                        $percent_bonus = $context->function_system->percent();
                        $add_bonus = floor(((int)$sum * ($percent_bonus)) / 100)
                        ?>
                        <p><b>У вас <?= Yii::$app->user->identity->bonus ?> бонусов</b></p>
                        <p>Мы начислим вам <b><?= $add_bonus ?></b> бонусов за этот заказ</p>
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
                            <?= $text_bonus ?>
                        </p>
                    <? endif ?>
                </div>
            <? endif ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$url_check_promo = Json::encode(Url::to(['api/cart']));
$this->registerJsFile('https://widget.cloudpayments.kz/bundles/cloudpayments');
$this->registerJs(<<<JS
$('.button_check_promo').on('click', function (e) {
    e.preventDefault();
    var element = $('input', '#check_promo');
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
            error: function () {
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
    $('.pickup-text').html('Выбран пункт - '+$('#popupPickUp .pickup-list li[data-id='+id+']').html());
    $('#pick-up').prop('checked', 'checked');
    
    popup({block_id: '#popupPickUp', action: 'close'});
});

$(document).on('click', "#cart_list .cartGoods .row a", function (e) {
    e.preventDefault();
    window.open(e.target.href, '_blank');
});

JS
);
?>
<style>
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
        font:  bold 1.2em/1.5em "Proxima Nova",sans-serif;
        border-bottom: 1px dotted #c7d7ed;
    }

    .popup-header {
        font-size: 2em;
        margin: 30px 0 20px 0;
    }

    #popupPickUp ul li {
        margin-bottom: 15px;
        color: #4686cc;
        font:  bold 1.5em "Proxima Nova",sans-serif;
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

    .cartGoods input[type=radio]:checked + label {
        font-weight: 600;
        color: #4686cc;
    }

    .cartGoods div.row {
        display: block;
        border: 1px solid #a6cbf5;
        border-radius: 4px;
        padding: 13px;
        margin-bottom: 10px;
        width: 40%;
        float: left;
        margin-right: 20px;
        height: 85px;
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

    .cartGoods .row label + p {
        margin-top: 10px;
        font: 1.3em "Proxima Nova",sans-serif;
    }

    .pickup-text {
        font-size: 1.6em;
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
