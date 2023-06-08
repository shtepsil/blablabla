<?php
/**
 * @var $this yii\web\View
 * @var $items common\models\Template[]
 */
use yii\helpers\Url;

?>
<section id="content">
	<div class="panel">
		<div class="panel-heading">
			<a href="<?= Url::to(['template/add']) ?>" class="btn-primary btn" data-hotkeys="ctrl+a">
				<i class="fa fa-plus"></i> <span class="hidden-xs hidden-sm">Добавить шаблон</span></a>
		</div>
		<table class="table-primary table table-striped table-hover">
			<colgroup>
				<col>
				<col width="150px">
				<col width="100px">
				<col width="100px">
				<col width="100px">
			</colgroup>
			<thead>
			<tr>
				<th>Название шаблона</th>
				<th class="hidden-xs">Изменен</th>
				<th class="text-right">Действия</th>
			</tr>
			</thead>
			<tbody>
				<?php foreach($items as $item): ?>
				    <tr id="layout_normal">
    					<th class="name">
    						<i class="fa fa-desktop"></i>
							<? if ($item->noDeleted): ?>
								<span class="label label-warning">Только чтение</span>
							<? endif ?>
    						<a href="<?=Url::to(['template/edit', 'id' => $item->id])?>"><?=$item->name?></a>
    					</th>
    					<td class="modified hidden-xs">
    						<?=date('d.m.y',$item->updated_at)?>
    					</td>
    					<td class="actions text-right">
							<? if (!$item->noDeleted): ?>
								<a href="<?= Url::to(['template/deleted', 'id' => $item->id]) ?>" class="btn-danger btn-xs btn-confirm btn">
									<i class="fa fa-times fa-inverse"></i>
								</a>
							<? endif ?>
    					</td>
    				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</section>