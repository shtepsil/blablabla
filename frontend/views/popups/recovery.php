<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 */
use frontend\form\Recovery;
use frontend\widgets\ActiveField;
//use frontend\widgets\ActiveForm;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$context = $this->context;
$model = new Recovery();
?>
<div id="popup_4">
    <div class="popup_content">
        <?php $form = ActiveForm::begin([
            'action' => Url::to(['site/send-form','f'=>'recovery']),
            'enableAjaxValidation' => false,
            'options' => ['enctype' => 'multipart/form-data'],
            'fieldClass'=>ActiveField::className(),
            'fieldConfig' => [
                'options' => ['class' => 'popup_line'],
//        'template' => "{label}<div class=\"col-md-10\">{input}\n{error}</div>",
                'template' => <<<HTML
<div class="popup_label">{label}</div>
<div class="popup_input">
	<div class="input_wrapper">
		{input}
	</div>
</div>
<div class="clear"></div>
HTML
                ,
            ],
            'events'=>[
                'afterValidate'=><<<JS
function(data){
  $(".popup_1").colorbox.resize();
}
JS

            ]
        ]); ?>
        <div class="popup_form">
            <div class="popup_title">Восстановление пароля</div>
            <?=$form->field($model, 'email');?>
            <input type="submit" value="Восстановить">
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>