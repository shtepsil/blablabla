<?php
/**
 *
 * @var \yii\web\View $this
 * @var $context \shadow\widgets\AdminForm
 * @var $order \common\models\Orders
 * @var $items \common\models\OrdersItems[]
 * @var $sets \common\models\OrdersSets[]
 * @var $form \shadow\widgets\AdminActiveForm
 * @var string $name
 */
use common\components\Debugger as d;
use common\models\City;
use common\models\OrdersItems;
use common\models\OrdersSets;
use yii\gii\TypeAheadAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

TypeAheadAsset::register($this);
$context = $this->context;
$items = $sets = $discount = [];

//$order->isWholesale = 0;

if (!$order->isNewRecord) {
    $items = OrdersItems::find()->with('item')->where(['order_id' => $order->id])->all();
    $sets = OrdersSets::find()->with('set')->where(['order_id' => $order->id])->all();
    /**
     * @var $functions \frontend\components\FunctionComponent
     */
    $functions = Yii::$app->function_system;
    $db_items = $sessions_items = [];
    foreach ($items as $key => &$value) {
        $value->populateRelation('item', $order->convert_to_model($value, $value->item));
        $db_items[$value->item->id] = $value->item;
        $sessions_items[$value->item->id] = $value->count;
    }
    if (!trim($order->discount)) {
        if ($order->isWholesale == 0) {
            $discount = $functions->discount_sale_items($db_items, $sessions_items);
        } else {
            $discount = [];
        }
    } else {
        $discount = [];
    }
} else {
    $order->price_delivery = 0;
}
$name = 'ordersItems';
$name_sets = 'ordersSets';
$in_id = $in_id_sets = []; //comment
?>
<div class="row">

    <div class="col-md-8">
        <?= Html::activeCheckbox($order, 'isPhoneOrder') ?>
    </div>
</div>
<div class="row"><strong>Товары</strong></div>
<div class="row">
    <div class="form-group col-md-12 form-control-static pull-left">
        <div style="vertical-align: top;display: inline-block;white-space: normal;zoom: 1;">
            <input class="form-control" style="width:400px;" type="text" id="new_<?= $name ?>" name="new_item[id]"
                data-id="0" placeholder="Введите название товара">
        </div>
        <div style="display: inline-block;vertical-align: top;zoom: 1;">
            <button type="button" class="btn-success btn" id="add-<?= $name ?>"><i
                    class="glyphicon glyphicon-plus"></i></button>
        </div>
    </div>
</div>
<? if (ORDER_ADD_ITEM)
    echo d::res(false, 'res-add-item') ?>
    <div class="table-responsive table-primary row">
        <table class="table table-striped table-hover">
            <colgroup>
                <col width="30%">
                <col width="100px">
                <col width="40px">
                <col width="60px">
                <col width="85px">
                <col width="100px">
                <col width="100px">
                <col width="50px">
            </colgroup>
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Цена<br> за ед.</th>
                    <th>Ед.<br> расчёта</th>
                    <th>Кол-во</th>
                    <th>Вес</th>
                    <th>Скидка</th>
                    <th>Стоимость</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody id="items-<?= $name ?>">
            <?php foreach ($items as $item): ?>
            <?php

                $item_params = [
                    'item' => $item,
                    'name' => $name,
                    'discounts' => $discount,
                ];

                if(isset($user) AND $user){
                    $item_params['user'] = $user;
                    $item_params['isWholesale'] = $user->isWholesale;
                }

                ?>
                <?= $this->render('item', $item_params) ?>
                <?php
                $in_id[$item->item->id] = $item->item->id;
                ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="row"><strong>Сеты</strong></div>
<div class="row">
    <div class="form-group col-md-12 form-control-static pull-left">
        <div style="vertical-align: top;display: inline-block;width: auto;white-space: normal;zoom: 1;">
            <input class="form-control" style="width:400px;" type="text" id="new_<?= $name_sets ?>" name="new_sets[id]"
                data-id="0" placeholder="Введите название товара">
        </div>
        <div style="display: inline-block;vertical-align: top;zoom: 1;">
            <button type="button" class="btn-success btn" id="add-<?= $name_sets ?>"><i
                    class="glyphicon glyphicon-plus"></i></button>
        </div>
    </div>
</div>
<div class="table-responsive table-primary row">
    <table class="table table-striped table-hover">
        <colgroup>
            <col>
            <col>
            <col>
            <col width="100px">
            <col width="50px">
        </colgroup>
        <thead>
            <tr>
                <th>Название</th>
                <th>Цена за ед.</th>
                <th>Количество</th>
                <th>Стоимость</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody id="items-<?= $name_sets ?>">
            <?php foreach ($sets as $set): ?>
                <?= $this->render('set', ['item' => $set, 'name' => $name_sets]) ?>
                <?php
                $in_id_sets[$set->set->id] = $set->set->id;
                ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="row">
    <div class="pull-right">
        <div class="row">
            <span>Сумма заказа:</span>
            <span class="text-right">
                <span id="order_sum">
                    <?= $order->full_price ?>
                </span>
                тенге.
            </span>
        </div>
        <div class="row">
            <div>Стоимость доставки:</div>
            <div>
                <?= Html::activeTextInput($order, 'price_delivery', ['class' => 'form-control', 'id' => 'price_delivery']) ?>
            </div>
        </div>
        <div class="row">
            <div>Скидка:</div>
            <div>
                <?= Html::activeTextInput($order, 'discount', ['class' => 'form-control', 'id' => 'price_discount']) ?>
            </div>
        </div>
        <div class="row">
            <span>Использованно бонусов:</span>
            <span class="text-right">
                <span id="bonus_price">
                    <?= $order->bonus_use ?>
                </span>
            </span>
        </div>
        <div class="row">
            <span>К оплате:</span>
            <span class="text-right">
                <span id="full_price">
                    <?= (($order->full_price + $order->price_delivery) - $order->discount($order->full_price)) - $order->bonus_use ?>
                </span>
                тенге.
            </span>
        </div>
        <? if (Yii::$app->user->can('manager')): ?>
            <div class="row">
                <span>Бонус менеджеру:</span>
                <span class="text-right">
                    <span id="full_bonus_manager">
                        <?= $order->bonus_manager ?>
                    </span>
                </span>
            </div>
        <? endif ?>
    </div>
</div>
<? if (!$order->isNewRecord): ?>
    <div class="row col-xs-12 col-md-6">
        <div class="row"><strong>Информация о клиенте:</strong></div>
        <div class="row">
            <strong>ФИО:</strong>
            <span class="text-right">
                <?= $order->user_name ?>
            </span>
        </div>
        <div class="row">
            <strong>Телефон:</strong>
            <span class="text-right">
                <?= $order->user_phone ?>
            </span>
        </div>
        <div class="row">
            <strong>Юридическое лицо:</strong>
            <span class="text-right">
                <?= ($order->isEntity) ? 'Да' : 'Нет' ?>
            </span>
        </div>
        <div class="row">
            <strong>Город:</strong>
            <span class="text-right">
                <?
                $city = false;
                if ($order->city_id) {
                    $city = City::findOne($order->city_id);
                }
                ?>
                <?= ($city) ? ($city->name) : 'Не выбран' ?>
            </span>
        </div>
        <div class="row">
            <strong>Адрес:</strong>
            <span class="text-right">
                <?= $order->user_address ?>
            </span>
        </div>
        <div class="row">
            <strong>Способ оплаты:</strong>
            <span class="text-right">
                <?= $order->data_payment[$order->payment] ?>
            </span>
        </div>
        <div class="row">
            <strong>Способ доставки:</strong>
            <span class="text-right">
                <?= (!empty($order->user_address) && $order->user_address == 'Самовывоз' ?
                    'Самовывоз' : 'Доставка') ?>
                <?php if ($order->delivery_method == '2')
                    echo ' Яндекс'; ?>
            </span>
        </div>
        <div class="row">
            <strong>Пункт самовывоза:</strong>
            <span class="text-right">
                <?php
                if (!empty($order->pickpoint_id)) {
                    $pickpoint = \common\models\Pickpoint::find()->where(['id' => $order->pickpoint_id])->one();
                }
                ?>
                <?= (!empty($pickpoint) ? $pickpoint->name : '-') ?>
            </span>
        </div>
        <? if (!in_array($order->delivery_method, [1, 2])): ?>
            <div class="row">
                <strong>Дата и время доставки:</strong>
                <span class="text-right">
                    <br />Дата:
                    <?= $order->date_delivery ?><br />
                    Время
                    <?= $order->time_delivery ?>
                </span>
            </div>
        <? endif ?>
        <div class="row">
            <strong>Комментарий:</strong>
            <span class="text-right">
                <?= $order->user_comments ?>
            </span>
        </div>
        <?php if (isset($order->hand_link)): ?>
            <div class="row">
                <strong>Cсылка для ручной отправки:</strong>
                <span class="text-right">
                    <?= $order->hand_link ?>
                </span>
            </div>
        <?php endif ?>
    </div>
<? endif ?>
<div class="padding-sm clearfix"></div>
<?php
$url_items = Url::to(['orders/items', 'search' => 'QUERY']);
$url_sets = Url::to(['orders/sets', 'search' => 'QUERY']);
$url_add_item = Url::to(['orders/add-item']);
$url_add_set = Url::to(['orders/add-set']);
$url_change_item = Url::to(['orders/change-item']);
$var_ids = Json::encode($in_id, JSON_FORCE_OBJECT);
$var_sets_ids = Json::encode($in_id_sets, JSON_FORCE_OBJECT);
$id_order = Json::encode($order->id);
$this->registerCss(<<<CSS
.tt-menu {
  max-height: 250px;
  overflow-y: auto;
  overflow-x: hidden;
}
CSS
);
$this->registerJs(<<<JS
var id_order={$id_order};
var ids_{$name} = {$var_ids};
var ids_{$name_sets} = {$var_sets_ids};
JS
    , $this::POS_HEAD
);
$this->registerJs(<<<JS
//JS
var name_{$name} = '{$name}[{new_index}][{value}]';
var index_{$name} = 0;

var bestItems = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
        url: '{$url_items}',
        wildcard: 'QUERY',
        prepare: function (query, settings) {
            settings.url = settings.url.replace('QUERY', encodeURIComponent(query));
            settings.data= {
                ids: ids_{$name}
            }
            return settings;
        },
    },
    limit: 100
});
bestItems.initialize();
$('#new_{$name}').typeahead({
    minLength: 2,
    highlight: true,
    hint: true,
    autoselect: true
}, {
    name: 'value',
    displayKey: 'value',
    source: bestItems.ttAdapter(),
	templates: {
        empty: [
            '<div class="list-group search-results-dropdown"><div class="list-group-item">Не найдено.</div></div>'
        ],
        header: [
            '<div style="width:400px" class="list-group search-results-dropdown">'
        ],
        suggestion: function (data) {
            return '<div style="width:400px"><img style="width:50px" src="' + data.img + '">' + data.value + '</div>'   
        }
	},
    limit: 100
}).on('typeahead:selected', function (event, data) {
    $(this).data('id', data.id);
    add_item(data.id);
}).on("focus", function () {
    if (checkIfCanOpen()) {
        var ev = $.Event("keydown");
        ev.keyCode = ev.which = 40;
        $(this).trigger(ev);
        return true;
    }
});
$('#add-{$name}').on('click', function () {
    var id = $('#new_{$name}').data('id');
    add_item(id);
});
function add_item(id) {
    var res = $('.res-add-item');
    
    res.html('result');

    if (id) {
        bestItems.clearRemoteCache()
        var isWholesale = $('#orders-iswholesale').val();
        
        $.ajax({
            url: "{$url_add_item}",
            type: 'GET',
            dataType: 'JSON',
            data: {id: id, order_id: id_order, isWholesale: isWholesale},
            success: function (data) {
                cl(data);
                res.html('Done<br><pre>' + prettyPrintJson.toHtml(data) + '</pre>');
                ids_{$name}[id] = data.id
                $('#items-{$name}').append(data.item);
                $.growl.notice({title: 'Успех', message: 'Добавлен новый товар'});
                $('#new_{$name}').typeahead('val', '');
                $('#new_{$name}').data('id', 0);
                update_price()
            },
            error: function (data) {
                res.html(JSON.stringify(data));
                $.growl.error({title: 'Ошибка', message: 'Произошла ошибка на стороне сервера', duration: 5000});
            }
        });
    } else {
        $('#new_{$name}').trigger('focus');
        $.growl.error({title: 'Ошибка', message: 'Необходимо выбрать из списка', duration: 5000});

    }
}
$('#items-{$name}').on('click', '.deleted-{$name}>a', function (e) {
    e.preventDefault();
    var id_item = $(this).data('id');
    $.each(ids_{$name}, function (i, el) {
        if (el == id_item) {
            delete ids_{$name}[i]
        }
    })
    bestItems.clearRemoteCache()
    $(this).parents('tr').remove();
    update_price();
}).on('change', 'input', function () {
    update_price();
});
var bestSets = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
        url: '{$url_sets}',
        wildcard: 'QUERY',
        prepare:function (query, settings) {
            settings.url = settings.url.replace('QUERY', encodeURIComponent(query));
            settings.data= {
                ids: ids_{$name_sets}
            }
            return settings;
        },
    },
    limit: 100
});
function checkIfCanOpen() {
    return $('#new_{$name}').val() && $('#new_{$name}').val().length > 2;
}
bestSets.initialize();
$('#new_{$name_sets}').typeahead({
    minLength: 3,
    highlight: true,
    hint: true,
    autoselect: true
}, {
    name: 'value',
    displayKey: 'value',
    source: bestSets.ttAdapter(),
    limit: 100
}).on('typeahead:selected', function (event, data) {
        $(this).data('id', data.id);
        add_sets(data.id);

    })
    .on("focus", function () {
        if (checkIfCanOpenSets()) {
            var ev = $.Event("keydown");
            ev.keyCode = ev.which = 40;
            $(this).trigger(ev);
            return true;
        }
    });
function checkIfCanOpenSets() {
    return $('#new_{$name_sets}').val() && $('#new_{$name_sets}').val().length > 2;
}
$('#add-{$name_sets}').on('click', function () {
    var id = $('#new_{$name_sets}').data('id');
    add_sets(id);

});
function add_sets(id) {
    if (id) {
        bestSets.clearRemoteCache()
        $.ajax({
            url: "{$url_add_set}",
            type: 'GET',
            dataType: 'JSON',
            data: {id: id},
            success: function (data) {
                ids_{$name_sets}[id] = data.id
                $('#items-{$name_sets}').append(data.item);
                $.growl.notice({title: 'Успех', message: 'Добавлен новый товар'});
                $('#new_{$name_sets}').typeahead('val', '');
                $('#new_{$name_sets}').data('id', 0)
                update_price()
            },
            error: function () {
                $.growl.error({title: 'Ошибка', message: 'Произошла ошибка на стороне сервера', duration: 5000});
            }
        });
    } else {
        $('#new_{$name_sets}').trigger('focus');
        $.growl.error({title: 'Ошибка', message: 'Необходимо выбрать из списка', duration: 5000});

    }
}
$('#items-{$name_sets}').on('click', '.deleted-{$name_sets}>a', function (e) {
    e.preventDefault();
    var id_item = $(this).data('id');
    $.each(ids_{$name_sets}, function (i, el) {
        if (el == id_item) {
            delete ids_{$name_sets}[i]
        }
    })
    bestSets.clearRemoteCache()
    $(this).parents('tr').remove();
    update_price();
}).on('change', 'input', function () {
    update_price();
});
$('#price_delivery').on('change', function () {
    update_price()
})
$('#price_discount').on('change', function () {
    update_price()
})
$('#orders-iswholesale input[type=radio]').on('change', function () {
    update_price()
})
$('[name=test_wholesale]').on('click', function(){
	update_price()
})
function update_price() {
    var res = $('.res');
    var sum = 0;
    var data_request = $('input', '#items-{$name},#items-{$name_sets}').serializeArray();
    var discount = $('#price_discount').val();
    var isWholesale = $('#orders-iswholesale input[type=radio]:checked').val();
	
    var price_delivery = $('#price_delivery').val();
    data_request.push({name: 'id', value: id_order})
    data_request.push({name: 'discount', value: discount})
    data_request.push({name: 'delivery', value: price_delivery})
    data_request.push({name: 'isWholesale', value: isWholesale});
	
	res.html('result');
	
    $.ajax({
        url: "{$url_change_item}",
        type: 'POST',
        dataType: 'JSON',
        data: data_request,
        success: function (data) {
            // cl('update_price');
            // cl(data);
            // res.html('Done<br><pre>' + prettyPrintJson.toHtml(data) + '</pre>');
            // return;
            edit_basket(data.items, '{$name}');
            edit_basket(data.sets, '{$name_sets}');
            $('#order_sum').text(data.sum);
            $('#full_price').text(data.full_price);
            $('#full_bonus_manager').text(data.full_bonus_manager);
        },
        error: function (data) {
            res.html('Fail<br>' + JSON.stringify(data));
            $.growl.error({title: 'Ошибка', message: 'Произошла ошибка на стороне сервера', duration: 5000});
        }
    });
//    $('input[data-price]', '#items-{$name}').each(function (i, el) {
//        var item_price = Math.round($(el).val() * $(el).data('price'));
//        $('.sum_item', $(el).parents('tr')).text(item_price)
//        sum = Math.round(sum + item_price);
//    });
//    $('input[data-price]', '#items-{$name_sets}').each(function (i, el) {
//        var item_price = Math.round($(el).val() * $(el).data('price'));
//        $('.sum_item', $(el).parents('tr')).text(item_price)
//        sum = Math.round(sum + item_price);
//    });
//    $('#order_sum').text(sum);
//    sum = parseInt($('#price_delivery').val()) + sum
//    sum = sum - (parseInt($('#bonus_price').text()))
//    $('#full_price').text(sum);
}
function edit_basket(items, type) {
    $.each(items, function (id, el) {
        if (typeof el.item_price != 'undefined') {
            $('.item_price input', '#' + type + '_' + id).val(el.item_price);
        }
        if (typeof el.price != 'undefined') {
            $('.sum_item', '#' + type + '_' + id).html(el.price);
        }
        if (typeof el.discount != 'undefined') {
            $('.sum_item_discount', '#' + type + '_' + id).html(el.discount);
        }
    })
}
JS
);
?>