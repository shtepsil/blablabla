<?php
/**
 * @var array $form_action
 * @var \yii\db\ActiveRecord | \yii\base\Model $item
 * @var \yii\web\View $this
 *
 */
//@var \shadow\widgets\AdminForm $this->context
use common\components\Debugger as d;
use shadow\helpers\SArrayHelper;
use shadow\widgets\AdminActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

$options_form = [ 'enctype' => 'multipart/form-data' ];
if(isset($form_options)){
    $options_form = ArrayHelper::merge($options_form, $form_options);
}

goto test_payment_types_widget;
$form_t = AdminActiveForm::begin([
    'action' => isset($form_action) ? $form_action : '',
    'enableAjaxValidation' => false,
    'options' => $options_form,
    'fieldConfig' => [
        'options' => ['class' => 'form-group simple'],
        'template' => "{label}<div class=\"col-md-10\">{input}\n{error}</div>",
        'labelOptions' => ['class' => 'col-md-2 control-label'],
    ],
]);
$u = \common\models\User::findOne(21277);
echo $form_t->field($u, 'payment_types')->widget(\kartik\select2\Select2::className(), [
    'name' => 'payment_types_widget',
    'value' => 'cash',
    'data' => [
        'cash' => 'Наличные',
        'online' => 'Онлайн оплата',
        'cards' => 'Банковской картой',
        'invoice' => 'Счёт для оплаты',
        'test' => 'Тестовая оплата',
    ],
    'options' => ['multiple' => true, 'placeholder' => ''],
]);

$form_t::end();
test_payment_types_widget:

?>
<div id="pageEdit">
    <?php $form = AdminActiveForm::begin([
        'action' => isset($form_action) ? $form_action : '',
        'enableAjaxValidation' => false,
        'options' => $options_form,
        'fieldConfig' => [
            'options' => ['class' => 'form-group simple'],
            'template' => "{label}<div class=\"col-md-10\">{input}\n{error}</div>",
            'labelOptions' => ['class' => 'col-md-2 control-label'],
        ],
    ]); ?>
    <?= Html::hiddenInput('id', $item->id) ?>
    <div style="position: relative;">
        <div class="form-actions panel-footer" style="padding-left: 0px;padding-top: 0px;">
            <?= Html::submitButton('<i class="fa fa-retweet"></i> Сохранить', ['class' => 'btn-success btn-save btn-lg btn', 'data-hotkeys' => 'ctrl+s', 'name' => 'continue']) ?>
            <?php if (isset($cancel)): ?>
                &nbsp;&nbsp;
                <button name="commit" type="submit" class="btn-save-close btn-default hidden-xs btn" onclick="$(this).val(1)">
                    <i class="fa fa-check"></i> Сохранить и Закрыть
                </button>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <a href="<?= Url::to(isset($cancel) ? $cancel : '') ?>" class="btn btn-close btn-sm btn-outline">
                    <i class="fa fa-ban"></i> <span class="hidden-xs hidden-sm">Отмена</span></a>
            <?php endif; ?>
        </div>
        <?php if (isset($groups) && $groups): ?>
            <?php
            $i_li = 0;
            $this->registerJs(<<<JS
function params_unserialize(p) {
    p = p.substr(1);
    var ret = {},
        seg = p.replace(/^\?/, '').split('&'),
        len = seg.length, i = 0, s;
    for (; i < len; i++) {
        if (!seg[i]) {
            continue;
        }
        s = seg[i].split('=');
        ret[s[0]] = s[1];
    }
    return ret;
}
if (location.hash !== '') {
    var hash = location.hash;
    if(hash){
        var hashs = params_unserialize(hash);
        if (hashs['tab']) {
            var obj=$('a[href="#' + hashs['tab'] + '"]');
            if($(obj).is(':visible')){
                $(obj).tab('show');
            }
        }
    }
}
$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    if (location.hash) {
        //location.hash = 'tab=' + $(e.target).attr('href').substr(1);
        var hash = location.hash;
        var hashs = params_unserialize(hash);
        hashs['tab'] = $(e.target).attr('href').substr(1);
        location.hash= $.param(hashs);
    } else {
        location.hash = 'tab=' + $(e.target).attr('href').substr(1);
    }
});
JS
            );
            ?>
            <?= Html::ul($groups,
                [
                    'class' => 'nav nav-tabs tabs-generated',
                    'item' => function ($item, $index) use (&$i_li) {
                        $context = $items_count = '';

                        if(isset($item['context'])){
                            /*
                             * getUserChildren - не правильно это тут делать так.
                             * Потому что тут должны быть общие скрипты, а не частные.
                             * Но пусть будет пока так.
                             * Если и вызывать тут подобный метод, то он должен быть
                             * для всех моделей одинаковым, но со своими настройками из FormParams.
                             * В общем надо будет потом тут это продумать.
                             */
                            $items_count = ' (' . (($item['context']->getUserChildren(true)) ?: '0') . ')';
                        }

                        $options = ['id' => "page-$index-panel-li"];
                        if (isset($item['icon']) && $item['icon']) {
                            $context = "<i class=\"fa fa-{$item['icon']}\"></i> ";
                        }
                        $context .= Html::tag('span',isset($item['title']) ? $item['title'] : $index,['class'=>'hidden-xs hidden-sm']);
                        if ($i_li == 0) {
                            $options['class'] = 'active';
                        }
                        if(isset($item['options'])){
                            $options = SArrayHelper::mergeOptions($options, $item['options']);
                        }
                        $result = Html::tag('li', Html::a($context  . $items_count, "#page-$index-panel", ['data-toggle' => 'tab']), $options);
                        $i_li++;
                        return $result;
                    }
                ]) ?>
        <?php endif ?>
    </div>
    <div class="panel form-horizontal">
        <?php if (isset($fields) && $fields): ?>
            <div class="panel-heading">
                <? foreach ($fields as $key_field => $config_field): ?>
                    <?= $this->context->getRow($form, $key_field, $config_field) ?>
                <? endforeach; ?>
            </div>
            <hr class="no-margin-vr" />
        <?php endif; ?>

        <?php if (isset($groups) && $groups): ?>
            <div class="tab-content no-padding-vr">
                <?php $i = 0; ?>
                <?php foreach ($groups as $key_group => $group): ?>
                    <?php
                    $class = 'fade';
                    $fields = array();
                    if ($i == 0) {
                        $class = 'active';
                    }
                    $i++;
                    if (isset($group['fields'])) {
                        $fields = $group['fields'];
                    }
                    if (isset($group['relation'])) {
                        $relation = $group['relation'];
                    }
                    ?>
                    <div class="tab-pane <?= $class ?>" id="page-<?= $key_group ?>-panel">
                        <?if (isset($group['tabBlockLayer']['view']) && is_bool($group['tabBlockLayer']['view']) && $group['tabBlockLayer']['view'] === true):?>
                            <?
                            $layer_params = ['form_name' => $form_name];
                            $layer_params['css_params'] = ['display' => 'none'];
                            if(isset($group['tabBlockLayer']['css_params'])){
                                $layer_params['css_params'] = SArrayHelper::merge($layer_params['css_params'], $group['tabBlockLayer']['css_params']);
                            }
                            ?>
                            <?= Yii::$app->controller->renderPartial('@shadow/widgets/views/blocks/tab-layer', $layer_params) ?>
                        <?endif?>
                        <?
                        $panel_attrs = ['class' => 'panel-body'];
                        if(isset($group['tabWrapAttrs']) AND count($group['tabWrapAttrs'])){
                            if(isset($group['tabWrapAttrs']['class'])){
                                Html::addCssClass($panel_attrs, $group['tabWrapAttrs']['class']);
                            }
                            unset($group['tabWrapAttrs']['class']);
                            $panel_attrs = SArrayHelper::merge($panel_attrs, $group['tabWrapAttrs']);
                        }
                        $panel_attrs = Html::renderTagAttributes($panel_attrs);
                        ?>
                        <div <?=$panel_attrs?>>
                            <div class="layer-loading"></div>
                            <?php if (isset($fields) && $fields): ?>
                                <? foreach ($fields as $key_field => $config_field): ?>
                                    <?=(isset($config_field['separator'])) ? $config_field['separator'] : ''?>
                                    <?=(isset($config_field['block_title'])) ? $config_field['block_title'] : ''?>
                                    <?= $this->context->getRow($form, $key_field, $config_field) ?>
                                <? endforeach; ?>
                            <?php endif; ?>
                            <?php if (isset($group['meta']) && $group['meta'] == true): ?>
                                <?= $this->render('meta', $_params_) ?>
                            <?php endif; ?>
                            <?php if (isset($group['render']) && isset($group['render']['view'])): ?>
                                <?php $render = $group['render'] ?>
                                <?php if (!isset($render['data'])): ?>
                                    <?= Yii::$app->controller->renderPartial($render['view'], $_params_+ ['form'=>$form]) ?>
                                <?php else: ?>
                                    <?= Yii::$app->controller->renderPartial($render['view'], $render['data']) ?>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (isset($relation) && $relation): ?>
                                <?=$this->context->getRelation($relation) ?>

                            <?php endif; ?>
                        </div>
                    </div>
                <? endforeach; ?>
            </div>
        <?php endif ?>
    </div>
    <?php AdminActiveForm::end(); ?>
</div>