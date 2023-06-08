<?php
/**
 *
 * @var $this \yii\web\View
 * @var $item \common\models\Items
 * @var $context \shadow\widgets\AdminForm
 * @var array $filters
 * @var string $name
 * @var double $count
 */
use yii\helpers\Html;
$name = 'SetsItems';
if(!isset($price)){
    $price = $item->price;
}
if(!isset($count)){
    $count = 0;
}
$context = $this->context;
?>
<tr class="item">
    <td>
        <?=$item->name ?>
    </td>
    <td>
        <?=$item->real_price()?>
    </td>
    <td>
        <?=Html::input('hidden',"{$name}[{$item->id}][add]",1)?>
        <?=Html::input('text',"{$name}[{$item->id}][price]",$price,['class'=>'form-control'])?>
    </td>
    <td>
        <?=Html::input('text',"{$name}[{$item->id}][count]",$count,['class'=>'form-control'])?>
    </td>
    <td class="actions text-center deleted-<?= $name ?>">
        <a href="#" class="btn btn-xs btn-danger" title="Удалить" data-id="<?=$item->id?>"><i class="fa fa-times fa-inverse"></i></a>
    </td>
</tr>
