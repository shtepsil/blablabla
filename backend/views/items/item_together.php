<?php
/**
 *
 * @var $this \yii\web\View
 * @var $item \common\models\Items
 * @var $main \common\models\Items
 * @var $context \shadow\widgets\AdminForm
 * @var array $filters
 * @var string $name
 * @var double $count
 */
use yii\helpers\Html;

$name = 'TogetherItems';
if (!isset($price)) {
    $price = '';
}
if (!isset($count)) {
    $count = 1;
} else {
    $count = (double)$count;
}
$context = $this->context;
?>

<? if (!isset($main) || !(isset($main) && ($main->id == $item->id))): ?>
    <tr class="item">
        <td>
            <?= $item->name ?>
        </td>
        <td>
            <?= $item->real_price() ?>
        </td>
        <td>
            <?= Html::input('hidden', "{$name}[{$item->id}][add]", $item->id) ?>
            <?= Html::input('text', "{$name}[{$item->id}][discount]", $price, ['class' => 'form-control']) ?>
        </td>
        <td>
            <?= Html::input('text', "{$name}[{$item->id}][count]", $count, ['class' => 'form-control']) ?>
        </td>
        <td class="actions text-center deleted-<?= $name ?>">
            <a href="#" class="btn btn-xs btn-danger" title="Удалить" data-id="<?= $item->id ?>"><i class="fa fa-times fa-inverse"></i></a>
        </td>
    </tr>
<? else: ?>
    <tr class="item">
        <td>
            Этот товар
        </td>
        <td>
        </td>
        <td>
            <?= Html::input('hidden', "{$name}[{$item->id}][add]", $item->id) ?>
            <?= Html::input('text', "{$name}[{$item->id}][discount]", $price, ['class' => 'form-control']) ?>
        </td>
        <td>
            <?= Html::input('text', "{$name}[{$item->id}][count]", $count, ['class' => 'form-control']) ?>
        </td>
        <td class="actions text-center">
        </td>
    </tr>
<? endif; ?>
