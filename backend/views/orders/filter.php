<?php
use yii\helpers\Html;
?>
    <div class="table-header block-filter clearfix">
        <?= Html::beginForm(['/admin/'], 'post', [
            'enctype' => 'multipart/form-data',
            'id' => 'order-filter'
        ]) ?>
        <div class="table-caption order-filters">
            <div class="order-filters-blocks">
                <div>
                    <label>Номер заказа
                        <?= Html::input('text', 'number', '', [
                            'class' => 'form-control input-sm',
                            'aria-controls' => 'jq-datatables'
                        ]) ?>
                    </label>
                </div>
                <div>
                    <label>Статусы заказа
                        <?= Html::dropDownList('status', null, $statuses, [
                            'class' => 'select_filter form-control',
                            'prompt' => 'Выберите...'
                        ]) ?>
                    </label>
                </div>
                <div>
                    <label>Менеджеры
                        <?= Html::dropDownList('manager', null, $managers, [
                            'class' => 'select_filter form-control',
                            'prompt' => 'Выберите...'
                        ]) ?>
                    </label>
                </div>
            </div>
            <div class="order-filters-blocks">
                <div>
                    <label>ФИО покупателя
                        <?= Html::input('text', 'buyer_name', '', ['class' => 'form-control input-sm']) ?>
                    </label>
                </div>
                <div>
                    <label>Email покупателя
                        <?= Html::input('text', 'buyer_email', '', ['class' => 'form-control input-sm']) ?>
                    </label>
                </div>
                <div>
                    <label>Телефон покупателя
                        <?= \yii\widgets\MaskedInput::widget([
                            'name' => 'buyer_phone',
                            'mask' => '+7(999)-999-9999',
                            'options' => [
                                'class' => 'form-control input-sm'
                            ],
                            'definitions' => [
                                'maskSymbol' => '_'
                            ]
                        ]) ?>
                    </label>
                </div>
            </div>
            <div class="order-filters-blocks">
                <div>
                    <label>Товар в заказе
                        <input type="text" name="good" class="form-control input-sm" placeholder="Название или артикул" aria-controls="jq-datatables">
                    </label>
                </div>
                <div>
                    <label>Сумма заказа
                        <div class="input-group" style="width: 223px;">
                            <?= Html::input('number', 'sum_start', '', ['class' => 'form-control input-sm']) ?>
                            <span class="input-group-addon">-</span>
                            <?= Html::input('number', 'sum_end', '', ['class' => 'form-control input-sm']) ?>
                        </div>
                    </label>
                </div>
            </div>
            <div class="order-filters-blocks">
                <div>
                    <label>Дата создания заказа
                        <div class="input-group datapicker-group" style="width: 230px;">
                            <?= Html::input('text', 'order_create_start', '', ['class' => 'form-control input-sm1']) ?>
                            <span class="input-group-addon">-</span>
                            <?= Html::input('text', 'order_create_end', '', ['class' => 'form-control input-sm1']) ?>
                        </div>
                    </label>
                </div>
                <div>
                    <label>Дата доставки заказа
                        <div class="input-group datapicker-group" style="width: 208px;">
                            <?= Html::input('text', 'order_delivery_start', '', ['class' => 'form-control input-sm1']) ?>
                            <span class="input-group-addon">-</span>
                            <?= Html::input('text', 'order_delivery_end', '', ['class' => 'form-control input-sm1']) ?>
                        </div>
                    </label>
                </div>
            </div>
            <div class="order-filters-blocks">
                <div>
                    <label>Типы оплаты
                        <?= Html::dropDownList('payment_type', null, $paymentType, [
                            'class' => 'select_filter form-control',
                            'prompt' => 'Выберите...'
                        ]) ?>
                    </label>
                </div>
                <div>
                    <label>Статусы оплаты
                        <?= Html::dropDownList('payment_status', null,  $paymentStatuses, [
                            'class' => 'select_filter form-control',
                            'prompt' => 'Выберите...'
                        ]) ?>
                    </label>
                </div>
            </div>
            <div class="order-filters-blocks">
                <div>
                    <label>Типы доставки
                        <?= Html::dropDownList('delivery', null, [
                            1 => 'Самовывоз',
                            2 => 'Курьер',
							3 => 'ЯндексДоставка'
                        ], [
                            'class' => 'select_filter form-control',
                            'prompt' => 'Выберите...'
                        ]) ?>
                    </label>
                </div>
                <div>
                    <label>Город
                        <?= Html::dropDownList('town', null, $towns, [
                            'class' => 'select_filter form-control',
                            'prompt' => 'Выберите...'
                        ]) ?>
                    </label>
                </div>
                <div>
                    <label>Точки самовывоза
                        <?= Html::dropDownList('pickpoint', null, $pickpoints, [
                            'class' => 'select_filter form-control',
                            'prompt' => 'Выберите...'
                        ]) ?>
                    </label>
                </div>
            </div>
        </div>
        <div id="jq-datatables_processing" class="dataTables_processing" style="display: none;">Processing...</div>
        <?= Html::endForm() ?>
        <?= \yii\helpers\Html::button('Сбросить всё', [
            'class' => 'btn btn-primary DT-lf-right',
            'id' => 'button-filter-clear'
        ]) ?>
    </div>
<?php
$this->registerJs(<<<JS
$( function() {
    $('.order-filters .order-filters-blocks .datapicker-group input').datepicker().on('changeDate', function(e) {
        e.preventDefault();
        var api = new $.fn.dataTable.Api(table_data);
        api.ajax.reload();
    }).on('clearDate', function(e) {
        e.preventDefault();
        var api = new $.fn.dataTable.Api(table_data);
        api.ajax.reload();
    });
    
    $(".order-filters .order-filters-blocks input[name=buyer_name], " +
     ".order-filters .order-filters-blocks input[name=buyer_email], " +
      ".order-filters .order-filters-blocks input[name=buyer_phone]").autocomplete({
          source: function(request, response) {
            var data = {};
            
            if ($(this)[0].element[0].name) {
                switch ($(this)[0].element[0].name) {
                    case 'buyer_name':
                        data = {'name' : request.term};
                        
                        break;
                    case 'buyer_email':
                        data = {'email' : request.term};
                        
                        break;
                    case 'buyer_phone':
                        data = {'phone' : request.term};
                        
                        break;
                }
            }
            
            if (data) {
                $.ajax( {
                  url: "/admin/search/orders-buyers.html",
                  dataType: "json",
                  method: 'get',
                  data: data,
                  success: function(data) {
                      response(data);
                  }
                });
            }
          },
          minLength: 3,
          select: function( event, ui ) {
              //log( "Selected: " + ui.item.value + " aka " + ui.item.id );
          }
    } );
    
    $( ".order-filters .order-filters-blocks input[name=goods]" ).autocomplete({
          source: function(request, response) {
            var data = {'text' : request.term};
            
            $.ajax( {
              url: "/admin/search/goods.html",
              dataType: "json",
              method: 'get',
              data: data,
              success: function(data) {
                  response(data);
              }
            });
          },
          minLength: 3,
          select: function( event, ui ) {
              //log( "Selected: " + ui.item.value + " aka " + ui.item.id );
          }
    } );
    
    $('.order-filters .order-filters-blocks input.input-sm, .order-filters .order-filters-blocks select').on( 'change', function (e) {
        e.preventDefault();
        var api = new $.fn.dataTable.Api(table_data);
        api.ajax.reload();
    });
    
    $('#button-filter').on( 'click', function (e) {
        e.preventDefault();
        
        if ($('.block-filter').is(':visible')) {
            $('.block-filter').hide();
        }
        else {
            $('.block-filter').show();
        }
    });
    
    $('#button-filter-clear').on( 'click', function (e) {
        e.preventDefault();
        
        $('.order-filters .order-filters-blocks input, .order-filters .order-filters-blocks select').each(function() {
          $(this).val('');
        });
        
        var api = new $.fn.dataTable.Api(table_data);
        api.ajax.reload();
    });
});

JS
);

$this->registerCss(<<<CSS
.block-filter {
    display: none;
}
.order-filters {
	display: flex;
	flex-direction: row;
	justify-content: flex-start;
	align-items: flex-start;
	flex-wrap: nowrap;
}

.order-filters .order-filters-blocks {
	margin-right: 30px;
}

.order-filters .order-filters-blocks:last-child {
    margin-right: 0;
}

.order-filters .order-filters-blocks > div > label {
    width: 100%;
}

.ui-autocomplete {
    max-height: 100px;
    overflow-y: auto;
    overflow-x: hidden;
}

#jq-datatables_filter {
    display: none;
}

.input-sm {
    height: 35px;
}

.input-sm1 {
    height: 35px;
}


CSS
    , ['type' => 'text/css']);