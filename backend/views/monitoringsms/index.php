<?php
/**
 * @var common\models\Category[] $cats
 * @var $this yii\web\View
 */
use backend\assets\CatalogAsset;
use common\models\Category;
use yii\helpers\Json;
use yii\helpers\Url;
use common\models\Orders;
use yii\widgets\LinkPager;
use yii\helpers\Html;

CatalogAsset::register($this);
?>
<?= $this->render('//blocks/breadcrumb') ?>
<section id="content">
	<div class="page-mail">	
	 <div class="table-header block-filter2 clearfix">
    <?= Html::beginForm(['monitoringsms/sendallsms'], 'get', [
            'id' => 'order-filter'
        ]) ?> 
        <div class="table-caption order-filters"> 
            <div class="order-filters-blocks">       
            <div class="order-filters-blocks">
                <div>
                    <label>РАССЫЛКА СМС<br> Инструкция: сначала рекомендовано запустить в тестовом режиме, чтобы посмотреть количество пользователей, которые попали в веденный диапазон id. Рекомендовано по 300 - 400 пользователей за один раз (но можно и больше в разумных пределах)<br>Введите id пользователей с... и по...
                        <div class="input-group" style="width: 230px;">
                            <?= Html::input('text', 'id_from', isset($_GET['id_from']) ? $_GET['id_from'] : '', ['class' => 'form-control input-sm1', 'autocomplete' => 'off', 'required' => true]) ?>
                            <span class="input-group-addon">-</span>
                            <?= Html::input('text', 'id_to', isset($_GET['id_to']) ? $_GET['id_to'] : '', ['class' => 'form-control input-sm1', 'autocomplete' => 'off', 'required' => true]) ?>					
                        </div>
                    </label>
					<?= Html::checkbox('test', isset($_GET['test']) ? true : '', ['label' => 'Тестовый режим']) ?>
							
                </div>
            </div>       
        </div>
    </div>
	<div class="form-group">
        <?= Html::submitButton('Рассылка', ['class' => 'btn btn-primary']) ?>		
    </div>
	<?= Html::endForm() ?>
	<?php if (isset($count_users)) echo 'Количество пользователей: ' . $count_users;?>
	</div>
	<hr>	
   <div class="table-header block-filter2 clearfix">
    <?= Html::beginForm(['monitoringsms/allsms'], 'get', [
            'id' => 'order-filter'
        ]) ?> 
        <div class="table-caption order-filters"> 
            <div class="order-filters-blocks">       
            <div class="order-filters-blocks">
                <div> 
                    <label>ПОЛУЧЕНИЕ ИЛИ ЭКСПОРТ ДАННЫХ ПО СМС<br> Введите период отправки смс с... и по...
                        <div class="input-group datapicker-group" style="width: 230px;">
                            <?= Html::input('text', 'date_from', isset($_GET['date_from']) ? $_GET['date_from'] : '', ['class' => 'form-control input-sm1', 'autocomplete' => 'off', 'required' => true]) ?>
                            <span class="input-group-addon">-</span>
                            <?= Html::input('text', 'date_to', isset($_GET['date_to']) ? $_GET['date_to'] : '', ['class' => 'form-control input-sm1', 'autocomplete' => 'off', 'required' => true]) ?>
                        </div>
                    </label>
                </div>
            </div>       
        </div>
    </div>
	<div class="form-group">
        <?= Html::submitButton('Получить', ['class' => 'btn btn-primary']) ?>		
    </div>
	<div class="input-group">
		<button class="btn btn-default" type="submit" onclick="$(this).val(1)" formtarget="_blank" name="export"><i class="fa fa-upload"></i> Экспорт
		</button>	
	</div> 
	<?= Html::endForm() ?>
	</div>
 	<?php if (!empty($user_res)) :?>
	<table class="table table-striped catalog_table" width="100%">
         <colgroup>
            <col width="110">
            <col width="100">
            <col width="150px">
        </colgroup>
        <thead>
        <tr>
			<th class="text-muted">ID</th>
			<th class="text-muted">USER ID</th>
			<th class="text-muted">Email</th>
			<th class="text-muted">Телефон</th>		
			<th class="text-muted">Дата отправки смс</th>
			<th class="text-muted">Дата последней успешной покупки</th>
			<th class="text-muted">Результат акции</th>
        </tr> 
        </thead>
        <tbody>		
			<?php foreach ($user_res as $res): ?>
				<tr <?php if ($res['created_at'] <= Orders::getLastOrder($res['id'])) echo 'style="background-color:#8bc34a5e"'; ?>>
					<td><?=$res['id']?></td>
					<td><?=$res['user_id']?></td>
					<td><?=$res->user->email?></td>
					<td><?=$res->user->phone?></td>
					<td><?=date('Y-m-d', $res['created_at'])?></td>
					<td><?php 
					$ord_ = Orders::getLastOrder($res['id']);
						if (isset($ord_)) {
							echo date('Y-m-d', $ord_);
						}										
					?></td>
					<td>
						<?php if ($res['created_at'] <= Orders::getLastOrder($res['id'])) :?>
							<div style="background:green">+</div>
						<?php else: ?>
							<div style="background:red">-</div>
						<?php endif ?>
					</td>
				</tr>
			<?php endforeach; ?>	
	</tbody>
    </table>
<?php
echo LinkPager::widget([
    'pagination' => $pages,
]);
?>
<?php endif?>    
</div>
</section>
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