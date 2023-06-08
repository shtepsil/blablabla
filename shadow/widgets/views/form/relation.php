<?php
/**
 *
 * @var array $form_action
 * @var \yii\db\ActiveRecord[] | \yii\base\Model[] $items
 * @var \yii\web\View $this
 * @var $context \shadow\widgets\AdminForm
 * @var array $attributes
 * @var string $name
 * @var \yii\db\ActiveRecord | \yii\base\Model $model
 */
use yii\helpers\Html;

$context = $this->context;
?>
    <div class="col-md-<?= isset($width) ? $width : 5 ?>">
        <table class="table table-primary table-striped table-hover">
            <colgroup>
                <col>
                <col width="10px">
            </colgroup>
            <thead>
            <tr>
                <?php foreach ($attributes as $key => $value): ?>
                    <?php if(is_array($value)): ?>
                        <?php if(isset($value['label'])): ?>
                            <th><?= $value['label'] ?></th>
                        <?php else: ?>
                            <th><?= $model->getAttributeLabel( $key ) ?></th>
                        <?php endif; ?>
                    <?php else: ?>
                        <th><?= $model->getAttributeLabel( $value ) ?></th>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if ((isset($add) && $add != false) || !isset($add)): ?>
                    <th>Действия</th>
                <?php endif; ?>
            </tr>
            </thead>
            <tbody id="items-<?= $name ?>">
            <?php foreach ($items as $item): ?>
                <tr class="item">
                    <?php foreach ($attributes as $key => $value): ?>
                        <?php if (!is_array($value)): ?>
                            <? if ($model->hasAttribute($value)): ?>
                                <td class="name">
                                    <?= $context->getRelationField($item, $name, $value) ?>
                                </td>
                            <? endif ?>
                        <?php else: ?>
                            <td class="name">
                                <?= $context->getRelationField($item, $name, $key, $value) ?>
                            </td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if ((isset($add) && $add != false) || !isset($add)): ?>
                        <td class="actions text-center deleted-<?= $name ?>">
                            <a href="#" class="btn btn-xs btn-danger" title="Удалить"><i class="fa fa-times fa-inverse"></i></a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            <?php if ((isset($add) && $add != false) || !isset($add)): ?>
                <tr class="item">
                    <?php foreach ($attributes as $key=>$value): ?>
                        <?php if (!is_array($value)): ?>
                            <? if ($model->hasAttribute($value)): ?>
                                <td class="name">
                                    <?= $context->getRelationField(null, $name, $value) ?>
                                </td>
                            <? endif; ?>
                        <?php else: ?>
                            <td class="name">
                                <?= $context->getRelationField(null, $name, $key, $value) ?>
                            </td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <td class="actions text-center add-<?= $name ?>">
                        <a href="#" class="btn btn-xs btn-primary" title="Добавить"><i class="fa fa-plus-circle fa-inverse"></i></a>
                    </td>
                    <td class="actions text-center hidden deleted-<?= $name ?>">
                        <a href="#" class="btn btn-xs btn-danger " title="Удалить"><i class="fa fa-times fa-inverse"></i></a>
                    </td>
                </tr>
                <tr class="item hidden clone_<?= $name ?>">
                    <?php foreach ($attributes as $key=>$value): ?>
                        <?php if (!is_array($value)): ?>
                            <? if ($model->hasAttribute($value)): ?>
                                <td class="name">
                                    <?= $context->getRelationField(null, $name, $value, [], true) ?>
                                </td>
                            <? endif ?>
                        <?php else: ?>
                            <td class="name">
                                <?= $context->getRelationField(null, $name, $key, $value, true) ?>
                            </td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <td class="actions text-center add-<?= $name ?>">
                        <a href="#" class="btn btn-xs btn-primary" title="Добавить"><i class="fa fa-plus-circle fa-inverse"></i></a>
                    </td>
                    <td class="actions text-center hidden deleted-<?= $name ?>">
                        <a href="#" class="btn btn-xs btn-danger" title="Удалить"><i class="fa fa-times fa-inverse"></i></a>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php
if ((isset($add) && $add != false) || !isset($add)) {
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
});
JS
    );
}
?>