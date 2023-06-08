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

CatalogAsset::register($this);
?>

<?= $this->render('//blocks/breadcrumb') ?>
<section id="content">
    <div class="page-mail">
        <div class="mail-nav">
            <div class="navigation">
                <div class="compose-btn">
                    <div class="btn-group">
                        <a href="<?= Url::to(['monitoring/allsms']) ?>" class="btn-primary btn"><i class="fa"></i>
                            <span class="hidden-xs hidden-sm">Запустить</span></a>
                    </div>
                </div>
            </div>
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
			<th class="text-muted">Email</th>
			<th class="text-muted">Телефон</th>
			<th class="text-muted">Количество sms</th>
			<th class="text-muted">Количество бонусов</th>
			<th class="text-muted">Дата последнего sms</th>			
			<th class="text-muted">Дата последней успешной покупки</th>
			<th class="text-muted">Результат акции</th>
        </tr> 
        </thead>
        <tbody>		
			<?php foreach ($user_res as $res): ?>
				<tr <?php if ($res['date_last_sms'] <= Orders::getLastOrder($res['id'])) echo 'style="background-color:#8bc34a5e"'; ?>>
					<td><?=$res['id']?></td>
					<td><?=$res['email']?></td>
					<td><?=$res['phone']?></td>
					<td><?=$res['count_sms']?></td>
					<td><?=$res['bonus']?></td>
					<td><?=date('Y-m-d', $res['date_last_sms'])?></td>
					<td><?php echo date('Y-m-d', Orders::getLastOrder($res['id']))?></td>
					<td>
					<?php if ($res['date_last_sms'] <= Orders::getLastOrder($res['id'])) :?>
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
// отображаем ссылки на страницы

echo LinkPager::widget([
    'pagination' => $pages,
]);

?>
	
 		<?php endif?>    
    </div>
</section>