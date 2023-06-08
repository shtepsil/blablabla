<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 */
use common\components\Debugger as d;
use frontend\form\CallbackSend;
use frontend\form\Recovery;
use frontend\widgets\ActiveField;
//use frontend\widgets\ActiveForm;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$context = $this->context;
$model = new CallbackSend();
?>
<?php $form = ActiveForm::begin([
    'action' => Url::to(['site/send-form', 'f' => 'callback']),
    'enableAjaxValidation' => false,
    'options' => ['enctype' => 'multipart/form-data', 'class' => 'formCallback'],
    'fieldClass' => ActiveField::className(),
    'fieldConfig' => [
        'options' => ['class' => 'string'],
        'template' => <<<HTML
{label}{input}
HTML
        ,
    ]
]); ?>
<?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(), [
    'mask' => '+7(999)-999-9999',
//        'definitions' =>[
//            'maskSymbol'=>'_'
//        ],
    'options'=>[
        'class'=>''
    ]
]); ?>
<div class="string">
    <button class="btn_Form blue" type="submit">Заказать звонок</button>
</div>
<?php ActiveForm::end(); ?>
