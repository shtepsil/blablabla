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


	 <div class="tab-pane fade2" id="page-history-panel">
		<div class="panel-body">
			<div class="table-responsive table-primary row col-xs-12">
				<table class="table table-striped table-hover">
					<colgroup>
						<col>						
						<col>
					</colgroup>                                  
						<thead>
						<tr>
							<th>Номер заказа</th>	
							<th>Итого с доставкой</th>								
						</tr>
						</thead>                                      
					<tbody>
					<?php foreach ($result_statmanager as $result): ?>
						<tr>						
							<td>
							<a href="<?= Url::to(['orders/control', 
							'id' => $result['id']	
							]) ?>"><?=$result['id']; ?></a>			
							</td>
							<td>
							<?=$result['summa']; ?>			
							</td>							
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

</section>