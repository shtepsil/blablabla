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
    <?= Html::beginForm(['analitics/getinfo'], 'get', [
            'id' => 'order-filter'
        ]) ?> 
        <div class="table-caption order-filters"> 
            <div class="order-filters-blocks">       
            <div class="order-filters-blocks">
                <div> 
                    <label>Графики<br> 					
						<div class="input-group-btn">
						<select name="type" class="form-control" style="width: 350px" tabindex="-1" title="">
							<option value="sel" <?php if (!isset($type)) echo 'selected';?>>Выберите тип графика</option>
							<option value="status" <?php if (isset($type) && $type == 'status') echo 'selected';?>>Аналитика по статусам</option>
							<option value="statistic" <?php if (isset($type) && $type == 'statistic') echo 'selected';?>>Статистика продаж</option>
							<option value="statmanager" <?php if (isset($type) && $type == 'statmanager') echo 'selected';?>>Статистика по менеджерам</option>
							<option value="statitems" <?php if (isset($type) && $type == 'statitems') echo 'selected';?>>Статистика по товарам</option>
							<option value="appanalitic" <?php if (isset($type) && $type == 'appanalitic') echo 'selected';?>>Статистика приложения и сайта</option>
						</select>
						</div>
						Введите период с... и по...					 									
                        <div class="input-group datapicker-group" style="width: 230px;">
                            <?= Html::input('text', 'date_from', isset($_GET['date_from']) ? $_GET['date_from'] : '', ['class' => 'form-control input-sm1', 'autocomplete' => 'off', 'required' => true]) ?>
                            <span class="input-group-addon">-</span>
                            <?= Html::input('text', 'date_to', isset($_GET['date_to']) ? $_GET['date_to'] : '', ['class' => 'form-control input-sm1', 'autocomplete' => 'off', 'required' => true]) ?>
                        </div>
                    </label>					
					<label>
					 <input type="checkbox" id="only_success_orders" value="1" name="only_success_orders" <?php if (!empty($_GET['only_success_orders'])) echo 'checked';?>>
					 только заказы со статусом Выполнено/Оплачено
					 </label><br>
					 <label>
					 <input type="checkbox" id="out_duplicate_orders" value="1" name="out_duplicate_orders" <?php if (!empty($_GET['out_duplicate_orders'])) echo 'checked';?>>
					 не учитывать заказы со статусом Дубликат
					 </label>
                </div>
            </div>       
        </div>
    </div>
	Обратите внимание на логику фильтра даты: c (дата входит в диапазон) до (даты, которая не входит в диапазон). Например, если вы выбрали с 01.02.2021 до 10.02.2021, то в результат попадут данные с 01.02.2021 по 09.02.2021 . Если нужно выбрать один день, например 05.03.2021, то в фильтре вы выбираете с 05.03.2021 до 06.03.2021
	<div class="form-group">
        <?= Html::submitButton('Получить', ['class' => 'btn btn-primary']) ?>		
    </div>
	<?= Html::endForm() ?>
	</div>
</div>
<?php if (isset($type) && $type == 'status') :?>
<hr>
<figure class="highcharts-figure">
    <div id="container"></div>
    <p class="highcharts-description">
	 Отображение заказов по статусам в период с <?=$_GET['date_from']?> по <?=$_GET['date_to']?> <br>
	 Количество заказов:<?=$count?>
    </p>
</figure>
<hr><br>
<figure class="highcharts-figure">
    <div id="container_"></div>
    <p class="highcharts-description">
       Отображение заказов по статусам в период с <?=$_GET['date_from']?> по <?=$_GET['date_to']?> <br>
	   Количество заказов:<?=$count?>
    </p>
</figure>
<tr><tr>
<div id="chartdiv"></div>
<div id="chartstatistic"></div>
<?php elseif (isset($type) && $type == 'statistic') : ?>
<div id="container_sta"></div><hr>
<div id="container_day_week"></div><hr>
<div id="container_hour"></div>
<div id="container_delivery_method"></div>
<?php elseif (isset($type) && $type == 'statmanager') : ?>
	 <div class="tab-pane fade2" id="page-history-panel">
		<div class="panel-body">
			<div class="table-responsive table-primary row col-xs-12">
				<table class="table table-striped table-hover">
					<colgroup>
						<col width="250px">
						<col width="250px">
						<col>
						<col>
					</colgroup>                                  
						<thead>
						<tr>
							<th>Сотрудник</th>
							<th>Сумма заказов</th>
							<th>Выполнено</th>
							<th>Отказы</th>
							<th>Чеки</th>
							<th>Средний чек</th>
						</tr>
						</thead>                                      
					<tbody>
					<?php foreach ($result_statmanager as $result): ?>
						<tr>
							<td>
								<?=$result['manager_id']; ?>
							</td>
							<td>
								<?=number_format($result['summa'], 0, ',', ' ')?> т.													
							</td>
							<td>  
								<?=number_format($result['summa_success'], 0, ',', ' ')?> т.									
							</td>
							<td>
								<?=number_format($result['summa_failure'], 0, ',', ' ')?> т.													
							</td>
							<td>
							<a href="<?= Url::to(['analitics/detailcheckmanager', 
								'manager_id' => $result['manager_id_'],
								'date_from' => $_GET['date_from'],
								'date_to' => $_GET['date_to']	
							]) ?>"><?=$result['cnt']; ?> шт.</a>							 
							</td>
							 <td>
							 <?=number_format($result['average_check'], 0, ',', ' ')?> т.
							 </td> 
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<?php elseif (isset($type) && $type == 'statitems') : ?>	
	<div class="tab-pane fade2" id="page-history-panel">
		<div class="panel-body">
			<div class="table-responsive table-primary row col-xs-12">
				<table class="table table-striped table-hover">
					<colgroup>
						<col width="250px">
						<col width="150px">
						<col>
					</colgroup>                                  
						<thead>
						<tr style="position:fixed; width:1030px; top:-10px;">
							<th width="250px">Товар</th>
							<th width="150px">Выручка</th>
							<th width="150px">Кол-во(шт.)</th>
							<th width="150px">Кол-во(кг.)</th>
							<th>Кол-во(б/д)</th>
							<th>Вес(б/д)</th>
							<th>Список id заказов</th>
						</tr> 
						<tr style="width:100%">
							<th width="250px">Товар</th>
							<th width="150px">Выручка</th>
							<th width="150px">Кол-во(шт.)</th>
							<th width="150px">Кол-во(кг.)</th>
							<th width="100px">Кол-во(б/д)</th>
							<th>Вес(б/д)</th>
							<th>Список id заказов</th>
						</tr> 						
						</thead>   				
					<tbody> 
					<?php foreach ($result_statitems_ as $key => $result): ?>				
						<tr>						
							<td>
								<?=$result['name'] ?>
							</td>
							<td>
								<?=number_format($result['summa'], 0, ',', ' ')?> т.													
							</td>
							<td>
							<?php if ($result['measure_price'] == ' шт.') :?>
							
							<?=$result['count']; ?> <?=$result['measure_price']; ?>
							<?php endif ?>
							
							</td>
							<td>
							<?php if ($result['measure_price'] ==' кг.') :?>
							
							<?=$result['count']; ?> <?=$result['measure_price']; ?>
							<?php endif ?>							
							</td>
								<td><?=$result['count_base']; ?> </td>
								<td><?=$result['weight_base']; ?> </td>														
							<td style="display:block;height:100px; overflow:auto"><?=$result['orders_']; ?></td>
						</tr>					
					<?php endforeach; ?>
					</tbody>					
				</table>
			</div>
		</div>
	</div>
<?php elseif (isset($type) && $type == 'appanalitic') : ?>
<hr>
<figure class="highcharts-figure">
    <div id="containerapp"></div>
    <p class="highcharts-description">
	 Отображение заказов по статусам в период с <?=$_GET['date_from']?> по <?=$_GET['date_to']?> <br>
	 Количество заказов:<?=$count?>
    </p>
</figure>
<hr><br>
<figure class="highcharts-figure">
    <div id="containerapp_"></div>
    <p class="highcharts-description">
       Отображение заказов по статусам в период с <?=$_GET['date_from']?> по <?=$_GET['date_to']?> <br>
	   Количество заказов:<?=$count?>
    </p>
</figure>
<tr><tr>
<div id="chartdivapp"></div>
<div id="chartstatisticapp"></div>
<?php elseif (isset($type) && $type == 'statistic') : ?>
<div id="container_sta"></div><hr>
<div id="container_day_week"></div><hr>
<div id="container_hour"></div>
<div id="container_delivery_method"></div>
<?php endif ?>
</section>
<?php if (isset($type) && $type == 'status') :?>
<?php $this->registerJs(<<<JS
	Highcharts.chart('container', {
		chart: {
			type: 'pyramid3d',
			options3d: {
				enabled: true,
				alpha: 10,
				depth: 50,
				viewDistance: 50
			}
		},
		title: {
			text: 'Заказы по статусам (тип - пирамида 3D)'
		},
		plotOptions: {
			series: {
				dataLabels: {
					enabled: true,
					format: '<b>{point.name}</b> ({point.y:,.0f})',
					allowOverlap: true,
					x: 10,
					y: -5
				},
				width: '60%',
				height: '80%',
				center: ['50%', '45%']
			}
		},
		series: [{
			name: 'Заказов: ',
			data: [

			{$result_for_table_str}
			]
		}]
	});

	Highcharts.chart('container_', {
		chart: {
			type: 'pie',
			options3d: {
				enabled: true,
				alpha: 45,
				beta: 0
			}
		},
		title: {
			text: 'Заказы по статусам (тип - пирог)'
		},
		accessibility: {
			point: {
				valueSuffix: '%'
			}
		},
		tooltip: {
			pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b> ({point.y:,.0f})'
		}, 
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				depth: 35,
				dataLabels: {
					enabled: true,
					format: '{point.name}'
				}
			}
		},
		series: [{
			type: 'pie',
			name: 'Заказов:',
			data: [
			{$result_for_table_str}
			]
		}]
	});

	am4core.ready(function() {

		// Themes begin
		am4core.useTheme(am4themes_animated);
		// Themes end

		// Create chart instance
		var chart = am4core.create("chartdiv", am4charts.PieChart);

		// Add data
		chart.data = [ {$result_for_table_str_}
		];

		// Add and configure Series
		var pieSeries = chart.series.push(new am4charts.PieSeries());
		pieSeries.dataFields.value = "litres";
		pieSeries.dataFields.category = "country";
		pieSeries.slices.template.stroke = am4core.color("#fff");
		pieSeries.slices.template.strokeOpacity = 1;

		// This creates initial animation
		pieSeries.hiddenState.properties.opacity = 1;
		pieSeries.hiddenState.properties.endAngle = -90;
		pieSeries.hiddenState.properties.startAngle = -90;

		chart.hiddenState.properties.radius = am4core.percent(0);
	}); 
JS
);
?>
<?php elseif (isset($type) && $type == 'statistic'): ?>
<?php $this->registerJs(<<<JS
	var averages = [
	{$str_sta}
		];

	Highcharts.chart('container_sta', {

		title: {
			text: 'Статистика продаж'
		},

		xAxis: {
			type: 'datetime',
			accessibility: {
				rangeDescription: 'Range: Jul 1st 2009 to Jul 31st 2009.'
			}
		},

		yAxis: {
			title: {
				text: null
			}
		},

		tooltip: {
			crosshairs: true,
			shared: true,
			valueSuffix: 'т.'
		},
	  
		series: [
		{
			name: 'Сумма',
			data: averages,
			zIndex: 1,
			marker: {
				fillColor: 'white',
				lineWidth: 2,
				lineColor: Highcharts.getOptions().colors[0]
			}
		}, 
		]
	});

	Highcharts.chart('container_day_week', {
		chart: {
			type: 'column'
		},
		title: {
			text: 'Статистика по дням недели'
		},
		subtitle: {
			text: ''
		},
		xAxis: {
			type: 'category',
			labels: {
				rotation: -45,
				style: {
					fontSize: '13px',
					fontFamily: 'Verdana, sans-serif'
				}
			}
		},
		yAxis: {
			min: 0,
			title: {
				text: 'Прибыль (т.)'
			}
		},
		legend: {
			enabled: false
		},
		tooltip: {
			pointFormat: 'Прибыль: <b>{point.y:.1f} т.</b>'
		},
		series: [{
			name: 'Population',
			data: [
			{$str_sta_day_week}

			],
			dataLabels: {
				enabled: true,
				rotation: -90,
				color: '#FFFFFF',
				align: 'right',
				format: '{point.y:.1f}', // one decimal
				y: 10, // 10 pixels down from the top
				style: {
					fontSize: '13px',
					fontFamily: 'Verdana, sans-serif'
				}
			}
		}]
	});

	Highcharts.chart('container_hour', {
		chart: {
			type: 'column'
		},
		title: {
			text: 'Статистика по часам'
		},
		subtitle: {
			text: ''
		},
		xAxis: {
			type: 'category',
			labels: {
				rotation: -45,
				style: {
					fontSize: '13px',
					fontFamily: 'Verdana, sans-serif'
				}
			}
		},
		yAxis: {
			min: 0,
			title: {
				text: 'Прибыль (т.)'
			}
		},
		legend: {
			enabled: false
		},
		tooltip: {
			pointFormat: 'Прибыль: <b>{point.y:.1f} т.</b>'
		},
		series: [{
			name: 'Population',
			data: [
			{$str_sta_hour}

			],
			dataLabels: {
				enabled: true,
				rotation: -90,
				color: '#FFFFFF',
				align: 'right',
				format: '{point.y:.1f}', // one decimal
				y: 10, // 10 pixels down from the top
				style: {
					fontSize: '13px',
					fontFamily: 'Verdana, sans-serif'
				}
			}
		}]
	});
	
	Highcharts.chart('container_delivery_method', {
		chart: {
			type: 'column'
		},
		title: {
			text: 'Статистика по методам доставки'
		},
		subtitle: {
			text: ''
		},
		xAxis: {
			type: 'category',
			labels: {
				rotation: -45,
				style: {
					fontSize: '13px',
					fontFamily: 'Verdana, sans-serif'
				}
			}
		},
		yAxis: {
			min: 0,
			title: {
				text: 'Прибыль (т.)'
			}
		},
		legend: {
			enabled: false
		},
		tooltip: {
			pointFormat: 'Прибыль: <b>{point.y:.1f} т.</b>'
		},
		series: [{
			name: 'Population',
			data: [ 
				{$str_sta_delivery_method}
			],
			dataLabels: {
				enabled: true,
				rotation: -90,
				color: '#FFFFFF',
				align: 'right',
				format: '{point.y:.1f}', // one decimal
				y: 10, // 10 pixels down from the top
				style: {
					fontSize: '13px',
					fontFamily: 'Verdana, sans-serif'
				}
			}
		}]
	});
JS
);
?>
<?php elseif (isset($type) && $type == 'appanalitic'): ?>
<?php $this->registerJs(<<<JS
	Highcharts.chart('containerapp', {
		chart: {
			type: 'pyramid3d',
			options3d: {
				enabled: true,
				alpha: 10,
				depth: 50,
				viewDistance: 50
			}
		},
		title: {
			text: 'Заказы приложения и сайта (тип - пирамида 3D)'
		},
		plotOptions: {
			series: {
				dataLabels: {
					enabled: true,
					format: '<b>{point.name}</b> ({point.y:,.0f})',
					allowOverlap: true,
					x: 10,
					y: -5
				},
				width: '60%',
				height: '80%',
				center: ['50%', '45%']
			}
		},
		series: [{
			name: 'Заказов: ',
			data: [

			{$result_for_table_str}
			]
		}]
	});

	Highcharts.chart('containerapp_', {
		chart: {
			type: 'pie',
			options3d: {
				enabled: true,
				alpha: 45,
				beta: 0
			}
		},
		title: {
			text: 'Заказы приложения и сайта (тип - пирог)'
		},
		accessibility: {
			point: {
				valueSuffix: '%'
			}
		},
		tooltip: {
			pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b> ({point.y:,.0f})'
		}, 
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				depth: 35,
				dataLabels: {
					enabled: true,
					format: '{point.name}'
				}
			}
		},
		series: [{
			type: 'pie',
			name: 'Заказов:',
			data: [
			{$result_for_table_str}
			]
		}]
	});

	am4core.ready(function() {

		// Themes begin
		am4core.useTheme(am4themes_animated);
		// Themes end

		// Create chart instance
		var chart = am4core.create("chartdivapp", am4charts.PieChart);

		// Add data
		chart.data = [ {$result_for_table_str_}
		];

		// Add and configure Series
		var pieSeries = chart.series.push(new am4charts.PieSeries());
		pieSeries.dataFields.value = "litres";
		pieSeries.dataFields.category = "country";
		pieSeries.slices.template.stroke = am4core.color("#fff");
		pieSeries.slices.template.strokeOpacity = 1;

		// This creates initial animation
		pieSeries.hiddenState.properties.opacity = 1;
		pieSeries.hiddenState.properties.endAngle = -90;
		pieSeries.hiddenState.properties.startAngle = -90;

		chart.hiddenState.properties.radius = am4core.percent(0);
	}); 
JS
);
?>
<?php endif ?>
<?php
$this->registerJs(<<<JS
$( function() {
    $('.order-filters .order-filters-blocks .datapicker-group input').datepicker().on('changeDate', function(e) {
        e.preventDefault();
    }).on('clearDate', function(e) {
        e.preventDefault();
    });
});
JS
);

$this->registerCss(<<<CSS

#chartdiv {
  width: 100%;
  height: 500px;
}

#chartdivapp {
  width: 100%;
  height: 500px;
}

.highcharts-figure, .highcharts-data-table table {
    min-width: 310px; 
    max-width: 800px;
    margin: 1em auto;
}

.highcharts-data-table table {
	font-family: Verdana, sans-serif;
	border-collapse: collapse;
	border: 1px solid #EBEBEB;
	margin: 10px auto;
	text-align: center;
	width: 100%;
	max-width: 500px;
}
.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}
.highcharts-data-table th {
	font-weight: 600;
    padding: 0.5em;
}
.highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
    padding: 0.5em;
}
.highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}
.highcharts-data-table tr:hover {
    background: #f1f7ff;
}

#container_ {
  height: 400px; 
}

#containerapp_ {
  height: 400px; 
}

CSS
    , ['type' => 'text/css']);
