<?php
/**
 *
 * @var $this \yii\web\View
 * @var $item \common\models\Items
 * @var $context \shadow\widgets\AdminForm
 * @var array $filters
 * @var string $name
 */
use common\models\Items;
use shadow\assets\Select2Assets;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>
<?php
Select2Assets::register($this);
$this->registerJs(<<<JS
$('.widget-select2-no').select2({
    width: '250px',
    language: 'ru'
});
JS
);
$name = 'itemAssociated';
$context = $this->context;
$q = new ActiveQuery(Items::className());
if(!$item->isNewRecord){
    $q->andWhere(['<>', 'id', $item->id]);
}
$db_items = $q->all();
$data = ArrayHelper::map($db_items, 'id', 'name');
$items = $item->AllAssociated();
?>
    <div class="table-primary col-md-5">
        <table class="table table-striped table-hover">
            <colgroup>
                <col width="250px">
                <col >
            </colgroup>
            <thead>
            <tr>
                <th>Товар</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody id="items-<?= $name ?>">
            <?php foreach ($items as $key=>$value): ?>
                <tr class="item">
                    <td class="name">
                        <?= Html::dropDownList($name . '[' . $value['id'] . '][item_id]', $value['item_id'], $data, ['class' => 'form-control widget-select2-no']) ?>
                    </td>

                    <td class="actions text-center deleted-<?= $name ?>">
                        <a href="#" class="btn btn-xs btn-danger" title="Удалить"><i class="fa fa-times fa-inverse"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr class="item">
                <td >
                    <?= Html::dropDownList($name . 'Clone[new][item_id]', '', $data, ['class' => 'form-control widget-select2-no', 'data-field' => 'item_id']) ?>
                </td>
                <td class="actions text-center add-<?= $name ?>">
                    <a href="#" class="btn btn-xs btn-primary" title="Добавить"><i class="fa fa-plus-circle fa-inverse"></i></a>
                </td>
                <td class="actions text-center hidden deleted-<?= $name ?>">
                    <a href="#" class="btn btn-xs btn-danger" title="Удалить"><i class="fa fa-times fa-inverse"></i></a>
                </td>
            </tr>
            <tr class="item hidden clone_<?= $name ?>">
                <td class="name">
                    <?= Html::dropDownList($name . 'Clone[new][item_id]', '', $data, ['class' => 'form-control widget-select2-no', 'data-field' => 'item_id']) ?>
                </td>
                <td class="actions text-center add-<?= $name ?>">
                    <a href="#" class="btn btn-xs btn-primary" title="Добавить"><i class="fa fa-plus-circle fa-inverse"></i></a>
                </td>
                <td class="actions text-center hidden deleted-<?= $name ?>">
                    <a href="#" class="btn btn-xs btn-danger" title="Удалить"><i class="fa fa-times fa-inverse"></i></a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
<?php
$this->registerJs(<<<JS
var name_{$name} = '{$name}[{new_index}][{value}]';
var index_{$name} = 0;
$('#items-{$name}').on('click', '.deleted-{$name}>a', function (e) {
    e.preventDefault();
    $(this).parents('tr').remove();
}).on('click', '.add-{$name}>a', function (e) {
    e.preventDefault();
    var tr_parent = $(this).parents('tr');
    var clone = $('.clone_{$name}').clone();
    index_{$name} = index_{$name} + 1;
    $('[name]', tr_parent).each(function (i, input) {
        var name_input = name_{$name};
        $(input).attr('name', name_input.replace('{new_index}', 'new' + index_{$name}).replace('{value}', $(input).data('field')));
    });
    $('.deleted-{$name}', tr_parent).removeClass('hidden');
    $('.add-{$name}', tr_parent).remove();

    clone.removeClass('hidden').removeClass('clone_{$name}');
    $(tr_parent).after(clone);
    $('.select2', clone).remove();
    $('.widget-select2-no', clone).select2({
        width: '250px',
        language: 'ru'
    });
});
JS
);
?>