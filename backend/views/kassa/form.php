<?php
/**
 * @var $item common\models\SHistoryMoney
 * @var $this yii\web\View
 * @var $context backend\controllers\OrdersController
 */
use backend\models\SUser;
use common\models\Orders;
use shadow\widgets\AdminActiveForm;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

$context = $this->context;
$cancel = $context->url['back'];
$all_order = Orders::find()->orderBy(['created_at' => SORT_DESC])->all();
$data_order = ArrayHelper::map($all_order,
    function ($el) {
        return $el->id;
    },
    function ($el) {
        return ('Заказ №' . $el->id);
    });
$query = new ActiveQuery(SUser::className());
$relation_data = $query->andWhere(['<>', 'id', Yii::$app->user->id])->all();
$all_users = ArrayHelper::map($relation_data, 'id', 'username');
$data_types = [
    1 => 'Для сдачи',
    2 => 'Перемещение денег',
    3 => 'Сдача заказа',
];
?>
<?= $this->render('//blocks/breadcrumb') ?>
<section id="content">
    <div id="order_edit">
        <?php $form = AdminActiveForm::begin([
            'action' => ['kassa/save'],
            'enableAjaxValidation' => false,
            'options' => ['enctype' => 'multipart/form-data'],
            'fieldConfig' => [
                'options' => ['class' => 'form-group simple'],
                'template' => "{label}<div class=\"col-md-10\">{input}\n{error}</div>",
                'labelOptions' => ['class' => 'col-md-2 control-label'],
            ],
        ]); ?>
        <div style="position: relative;">
            <div class="form-actions panel-heading" style="padding-left: 0px;padding-top: 0px;">
                <div class="row">
                    <button name="commit" type="submit" class="btn-success btn" onclick="$(this).val(1)" title="Отправить">
                        <i class="fa fa-arrow-up"></i> <span class="hidden-xs hidden-sm">Отправить</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="panel form-horizontal">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-8">
                        <?= $form->field($item, 'type')->dropDownList($data_types) ?>
                        <?= $form->field($item, 'order_id')->dropDownList($data_order) ?>
                        <?= $form->field($item, 'recipient_user_id')->dropDownList($all_users) ?>
                        <?= $form->field($item, 'sum') ?>
                    </div>
                </div>
            </div>
        </div>
        <?php AdminActiveForm::end(); ?>
    </div>
</section>
<?php
$form_name = strtolower($item->formName());
$this->registerJs(<<<JS
$('#{$form_name}-type').on('change', function (e) {
    if ($(this).val() == 2) {
        $('.field-{$form_name}-order_id').hide();
    } else {
        $('.field-{$form_name}-order_id').show();
    }
})
JS
)
?>