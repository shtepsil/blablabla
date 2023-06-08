<?php
/**
 * @var $this yii\web\View
 * @var $contectx yii\web\View
 */

use common\components\Debugger as d;
use backend\models\SUser;
use common\models\Orders;
use shadow\assets\DataTablesAssets;
use yii\helpers\Json;
use yii\helpers\Url;

DataTablesAssets::register($this);
$urlFilter = ['orders/filter'];

$get = Yii::$app->request->get();

if (isset($get['filter_menu'])) {
    $urlFilter['filter_menu'] = $get['filter_menu'];
}

$url = Url::to($urlFilter);
$url_export = Url::to(['orders/export']);
$custom_filter = [
    3 => [
        'type' => "select",
        'values' => (new Orders())->data_status
    ],
    4 => [
        'type' => "date-range"
    ],
    5 => [
        'type' => "date-range"
    ]
];
if (Yii::$app->user->can('admin')) {
    $custom_filter[2] = [
        'type' => "select",
        'values' => SUser::find()
            ->where(['role' => ['manager', 'collector', 'driver']])
            ->select(['username', 'id'])->indexBy('id')->column()
    ];
}
$custom_filter_js = Json::encode($custom_filter);

//d::pre(Yii::$app->user->getId());

$this->registerJs(<<<JS
/*$.fn.datepicker.defaults.format = "mm.dd.yy";
$.fn.datepicker.defaults.language = "ru";*/
JS
    , $this::POS_END
);
$this->registerJs(<<<JS
JS
    , $this::POS_LOAD
);
$this->registerJs(<<<JS
//JS
jQuery.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
{
    return {
        "iStart":         oSettings._iDisplayStart,
        "iEnd":           oSettings.fnDisplayEnd(),
        "iLength":        oSettings._iDisplayLength,
        "iTotal":         oSettings.fnRecordsTotal(),
        "iFilteredTotal": oSettings.fnRecordsDisplay(),
        "iPage":          oSettings._iDisplayLength === -1 ?
            0 : Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
        "iTotalPages":    oSettings._iDisplayLength === -1 ?
            0 : Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
    };
};

var table_data = $('#jq-datatables').dataTable({
        processing: true,
		"bStateSave": true,	
        serverSide: true,
		stateSave: true,
        nTHead: 'thead tr:first',
        ajax: {
            url: '$url',
            'type': 'POST',
            data: function (d) {
                d.data = $('#order-filter').serializeArray();
            },
			error: function (xhr, error, code)
            {
                console.log(xhr);
                console.log(code);
            }
        },
        order: [[4, "desc"]],
        columns: [
            {
                className: 'details-control',
                orderable: false,
                data: null,
                defaultContent: ''
            },
            {data: "user_name", type: 'html'},
            {data: "performer", orderable: false},
            {data: "status"},
            {data: "created_at", 'class': 'text-center'},
            {data: "date_delivery", 'class': 'text-center'},
            {data: "actions_model", orderable: false, type: 'html', 'class': 'actions text-right'}
        ],
        rowCallback: function( row, data, displayIndex, displayIndexFull ) {
            var api = this.api();
            var row_tr = api.row(row);
            row_tr.child(format(data)).show();
            $(row).addClass('shown');
        }
    })
    /*.columnFilter({
        sPlaceHolder: 'head:after',
        aoColumns: {$custom_filter_js},
    })*/
;
function format(d) {
    var color = 'red';
    // `d` is the original data object for the row
    var result =
        '<tr>' +
            '<td style="background:' + d.new_order_color+'">' + d.isApp + 'Заказ №</td>' +
            '<td  style="padding-left: 8px;">' + d.id + '</td>' +
        '</tr>' +
        '<tr>' +
            '<td>Сумма заказа:</td>' +
            '<td  style="padding-left: 8px;">' + d.full_price + '</td>' +
        '</tr>' +
		'<tr>' +
            '<td>Итого с доставкой:</td>' +
            '<td  style="padding-left: 8px;">' + d.price_delivery_full + '</td>' +
        '</tr>' +
		'<tr>' +
            '<td>Использовано бонусов:</td>' +
            '<td  style="padding-left: 8px;">' + d.bonus_use + '</td>' +
        '</tr>' +
        '<tr>' +
            '<td>Телефон:</td>' +
            '<td  style="padding-left: 8px;">' + d.user_phone + '</td>' +
        '</tr>' +
        '<tr>' +
            '<td>Адрес:</td>' +
            '<td style="padding-left: 8px;">' + d.user_address + '</td>' +
        '</tr>';
    if (d.payment == 2 ){
        result +=
        '<tr>' +
            '<td>Способ оплаты:</td>' +
            '<td style="padding-left: 8px;color:red;">Онлайн оплата банковской картой</td>' +
        '</tr>';

        if(d.pay_status) {
            switch (d.pay_status){
                case 'send_pay':
                    break;
                case 'wait':
                    color = 'red';
                    break;
                case 'success_surcharge':
                    color = 'green';
                    break;
                case 'success':
                    color = 'green';
                    break;
                case 'success_rollback':
                    color = '#ce8d14';
                    break;
            }
            result +=
            '<tr>' +
                '<td>Статус онлайн платежа:</td>' +
                '<td style="padding-left: 8px;color:' + color + ';">' + (d.pay_status_text) + '</td>' +
            '</tr>';
        }
    }else if(d.payment == 1){
        result +=
        '<tr>' +
            '<td>Способ оплаты:</td>' +
            '<td style="padding-left: 8px;">Наличные</td>' +
        '</tr>';
    }else if(d.payment == 3){
        result +=
        '<tr>' +
            '<td>Способ оплаты:</td>' +
            '<td style="padding-left: 8px;">Банковской картой при получении</td>' +
        '</tr>';
    }else if(d.payment == 4){
        result +=
            '<tr>' +
                '<td>Способ оплаты:</td>' +
                '<td style="padding-left: 8px;">Счёт для оплаты (оптовикам)</td>' +
            '</tr>';
        if(d.pay_status) {
            switch (d.pay_status){
                case 'send_pay':
                    break;
                case 'wait':
                    color = 'red';
                    break;
                case 'success_surcharge':
                    color = 'green';
                    break;
                case 'success':
                    color = 'green';
                    break;
                case 'success_rollback':
                    color = '#ce8d14';
                    break;
            }
            result +=
                '<tr>' +
                    '<td>Статус оплаты:</td>' +
                    '<td style="padding-left: 8px;color:' + color + ';">' + (d.pay_status_text) + '</td>' +
                '</tr>';		
        }
    }
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'
        +result+
        '</table>';
}

setInterval(function(){ 
	var pageIndex = table_data.fnPagingInfo().iPage;  	
	var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
	if (pageIndex == 0 && scrollTop < 400)table_data.fnDraw(false);		
}, 5000);

$('#jq-datatables tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    var api = new $.fn.dataTable.Api(table_data);
    var row = api.row(tr);

    if (row.child.isShown()) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        // Open this row
        row.child(format(row.data())).show();
        tr.addClass('shown');
    }
});
$('#export_orders').on('click',function(e) {
    e.preventDefault();
    var form_export='<form id="form_export" action="{$url_export}" method="GET" target="_blank">' +
        '<label class="control-label">Период</label>' +
        '<div class="input-daterange input-group">' +
        '<input type="text" id="date_start_export" name="date_start" class="input-sm form-control" >' +
        '<span class="input-group-addon">-</span>' +
        '<input type="text" class="input-sm form-control" id="date_end_export" name="date_end" >' +
        '</div>' +
        '</form>'
    var box_message = bootbox.confirm(form_export, function (result) {
        if (result) {
             $('#form_export').submit()
        }
    });
    box_message.on("shown.bs.modal", function () {
        $('#date_start_export').datepicker();
        $('#date_end_export').datepicker();
    });
})

JS
);
$this->registerCss(<<<CSS
.filter_date_range input {
	color: #333333;
}

.select_filter {
	max-width: 240px!important;
}

td.details-control {
	cursor: pointer;
}

td.details-control:before {
	content: "\\f055";
	cursor: pointer;
	display: inline-block;
	font: normal normal normal 14px/1 FontAwesome;
	font-size: inherit;
	text-rendering: auto;
	-webkit-font-smoothing: antialiased;
	-moz-osx-font-smoothing: grayscale;
	transform: translate(0, 0);
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

tr.shown td.details-control:before {
	content: "\\f056";
}

CSS
    ,
    ['type' => 'text/css']
);
$url = 'orders';
?>
<?= $this->render('//blocks/breadcrumb') ?>
<section id="content">
    <div class="panel">
        <? if (Yii::$app->user->can('manager')): ?>
            <div class="panel-heading">
                <a href="<?= Url::to([$url . '/control']) ?>" class="btn-primary btn">
                    <i class="fa fa-plus"></i> <span class="hidden-xs hidden-sm">Добавить</span></a>
                <a href="<?= Url::to([$url . '/export']) ?>" class="btn-primary btn" id="export_orders">
                    <i class="fa fa-upload"></i> <span class="hidden-xs hidden-sm">Экспорт Excel</span></a>
                <?= \yii\helpers\Html::button('Фильтр', ['class' => 'btn btn-primary', 'id' => 'button-filter']) ?>
            </div>
        <? elseif (Yii::$app->user->can('collector')): ?>
            <div class="panel-heading">
                <a href="<?= Url::to([$url . '/export']) ?>" class="btn-primary btn" id="export_orders">
                    <i class="fa fa-upload"></i> <span class="hidden-xs hidden-sm">Экспорт Excel</span></a>
                <?= \yii\helpers\Html::button('Фильтр', ['class' => 'btn btn-primary', 'id' => 'button-filter']) ?>
            </div>
        <? endif ?>
        <div class="panel-body">
            <div class="table-primary table-responsive">
                <?= $filter = $this->render('//orders/filter', [
                    'managers' => $managers,
                    'paymentType' => $paymentType,
                    'statuses' => $statuses,
                    'paymentStatuses' => $paymentStatuses,
                    'pickpoints' => $pickpoints,
                    'url' => $url,
                    'towns' => $towns
                ]); ?>
                <table class="table table-striped table-hover table-bordered" id="jq-datatables">
                    <colgroup>
                        <col width="25px">
                        <col width="250px">
                        <col width="150px">
                        <col width="100px">
                        <col width="200px">
                        <col width="120px">
                        <col width="100px">
                    </colgroup>
                    <thead>
                        <tr>
                            <th></th>
                            <th>Пользователь</th>
                            <th>Исполнитель</th>
                            <th>Статус</th>
                            <th class="text-center">Дата создания</th>
                            <th class="text-center">Дата доставки</th>
                            <th class="text-right">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <? if (0): ?>
                            <?php foreach ($items as $item): ?>
                                <tr id="layout_normal">
                                    <th class="text-center">
                                        <a href="<?= Url::to([$url . '/control', 'id' => $item->id]) ?>"><?= $item->id ?></a>
                                    </th>
                                    <th>
                                        <a href="<?= Url::to([$url . '/control', 'id' => $item->id]) ?>">
                                            <?= $item->user_name ?>
                                        </a>
                                    </th>
                                    <th>
                                        <?= $item->data_status[$item->status] ?>
                                    </th>
                                    <th class="text-center">
                                        <?= date('d.m.y', $item->created_at) ?>
                                    </th>
                                    <td class="actions text-right">
                                        <a href="<?= Url::to([$url . '/deleted', 'id' => $item->id]) ?>"
                                            class="btn-danger btn-xs btn-confirm btn">
                                            <i class="fa fa-times fa-inverse"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <? endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>