<?php
/**
 *
 * @var $this \yii\web\View
 * @var $form \shadow\widgets\AdminActiveForm
 * @var $item \common\models\Recipes
 * @var $context \shadow\widgets\AdminForm
 * @var array $filters
 * @var string $name
 * @var $methods RecipesMethod[]
 */
use common\models\Items;
use common\models\RecipesMethod;
use shadow\assets\Select2Assets;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>
<?php
$name = 'recipesMethod';
$context = $this->context;
if (!$item->isNewRecord) {
    $methods = RecipesMethod::find()->andWhere(['recipe_id' => $item->id])->orderBy(['sort' => SORT_ASC])->all();
} else {
    $methods = [];
}
$id_form = $form->id;
$this->registerJs(<<<JS
$('#{$id_form}').on('beforeValidate', function (e) {
    var errors = false;
    var count = $('.{$name}-body', '#items-{$name}').length - 2
    $('.{$name}-body', '#items-{$name}').each(function (i, el) {
        if (count < (i + 1)) {
            return;
        }
        if ($.trim($(el).val()) == '') {
            errors = true;
            if (!$(el).next('span').length) {
                var error_span = $('<span class="help-block">Поле не может быть пустым</span>');
                $(el).parents('td').addClass('has-error');
                $(el).after(error_span);
            }
        } else {
            if ($(el).next('span').hasClass('help-block')) {
                $(el).next('span').remove();
                $(el).parents('td').removeClass('has-error');
            }
        }
    })
    if (errors) {
        var obj = $('a[href="#page-recipe_method-panel"]');
        if ($(obj).attr('aria-expanded') == 'false') {
            $(obj).tab('show');
        }
        $.growl.error({title: 'Ошибка', message: 'Поле "Текст" в способах приготовления обязательно для заполнения', duration: 7000});
        return false;
    } else {
        return true;
    }
})
JS
)
?>
    <div class="table-primary">
        <table class="table table-striped table-hover">
            <colgroup>
                <col width="250px">
                <col>
                <col>
                <col width="50px">
                <col width="50px">
            </colgroup>
            <thead>
            <tr>
                <th>Изображение</th>
                <th>Заголовок</th>
                <th>Текст *</th>
                <th>Порядок</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody id="items-<?= $name ?>">
            <?php foreach ($methods as $method): ?>
                <tr class="item">
                    <td>
                        <?= Html::input('file', $name . '[' . $method->id . '][img]', $method->img, ['class' => 'form-control']) ?>
                        <div>
                            <img src="<?= $method->img ?>" alt="" style="max-height: 150px;">
                        </div>
                    </td>
                    <td>
                        <?= Html::input('text', $name . '[' . $method->id . '][name]', $method->name, ['class' => 'form-control']) ?>
                    </td>
                    <td>
                        <?= Html::textarea($name . '[' . $method->id . '][body]', $method->body, ['class' => 'form-control' . " {$name}-body"]) ?>
                    </td>
                    <td>
                        <?= Html::input('text', $name . '[' . $method->id . '][sort]', $method->sort, ['class' => 'form-control']) ?>
                    </td>
                    <td class="actions text-center deleted-<?= $name ?>">
                        <a href="#" class="btn btn-xs btn-danger" title="Удалить"><i class="fa fa-times fa-inverse"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr class="item">
                <td>
                    <?= Html::input('file', $name . '[new][img]', '', ['class' => 'form-control', 'data-field' => 'img']) ?>
                </td>
                <td>
                    <?= Html::input('text', $name . '[new][name]', '', ['class' => 'form-control', 'data-field' => 'name']) ?>
                </td>
                <td>
                    <?= Html::textarea($name . '[new][body]', '', ['class' => 'form-control' . " {$name}-body", 'data-field' => 'body']) ?>
                </td>
                <td>
                    <?= Html::input('text', $name . '[new][sort]', '', ['class' => 'form-control', 'data-field' => 'sort']) ?>
                </td>
                <td class="actions text-center add-<?= $name ?>">
                    <a href="#" class="btn btn-xs btn-primary" title="Добавить"><i class="fa fa-plus-circle fa-inverse"></i></a>
                </td>
                <td class="actions text-center hidden deleted-<?= $name ?>">
                    <a href="#" class="btn btn-xs btn-danger " title="Удалить"><i class="fa fa-times fa-inverse"></i></a>
                </td>
            </tr>
            <tr class="item hidden clone_<?= $name ?>">
                <td>
                    <?= Html::input('file', $name . 'Clone[new][img]', '', ['class' => 'form-control', 'data-field' => 'img']) ?>
                </td>
                <td>
                    <?= Html::input('text', $name . 'Clone[new][name]', '', ['class' => 'form-control', 'data-field' => 'name']) ?>
                </td>
                <td>
                    <?= Html::textarea($name . 'Clone[new][body]', '', ['class' => 'form-control' . " {$name}-body", 'data-field' => 'body']) ?>
                </td>
                <td>
                    <?= Html::input('text', $name . 'Clone[new][sort]', '', ['class' => 'form-control', 'data-field' => 'sort']) ?>
                </td>
                <td class="actions text-center add-<?= $name ?>">
                    <a href="#" class="btn btn-xs btn-primary" title="Добавить"><i class="fa fa-plus-circle fa-inverse"></i></a>
                </td>
                <td class="actions text-center hidden deleted-<?= $name ?>">
                    <a href="#" class="btn btn-xs btn-danger " title="Удалить"><i class="fa fa-times fa-inverse"></i></a>
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
});
JS
);
?>