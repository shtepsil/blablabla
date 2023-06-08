<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 */
use frontend\form\CallbackSend;
use frontend\form\FastOrder;
use frontend\form\Recovery;
use frontend\widgets\ActiveField;
//use frontend\widgets\ActiveForm;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$context = $this->context;
$model = new FastOrder();
$model->type = 1;
if(!Yii::$app->user->isGuest){
    $model->name = $context->user->username;
    $model->phone = $context->user->phone;
}
?>
<div id="fastOrder" class="popup window">
    <div class="popupClose" onclick="popup({block_id: '#fastOrder', action: 'close'});"></div>
    <div class="popupTitle">Быстрый заказ</div>
    <div class="popupText">
        <p>Укажите свой контактный телефон, и мы Вам перезвоним</p>
    </div>
    <?php $form = ActiveForm::begin([
        'action' => Url::to(['site/send-form', 'f' => 'fast_order']),
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
    <?=Html::activeHiddenInput($model,'type')?>
    <?=Html::activeHiddenInput($model,'items')?>
    <?= $form->field($model, 'name'); ?>
    <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(), [
        'mask' => '+7(799)-999-9999',
        'definitions' =>[
            'maskSymbol'=>'_'
        ],
        'options'=>[
            'class'=>''
        ]
    ]); ?>
    <div class="string">
        <button class="btn_Form blue" type="submit">Заказать звонок</button>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs(<<<JS

$("form.formCallback").on('beforeSubmit', function(){  

var url = $(this).attr('action');

	var data = $(this).serialize();
	$.ajax({
		url: url,
		type: 'POST', 
		data: data,
		success: function(res){ 
		
		console.log(res);
		
			if (typeof res.message != 'undefined') {
				$.growl.notice({title: 'Успех', message: res.message['success']});
				window.location.href = '/';
			}else if (typeof res.errors['login-login'] != 'undefined') {  
					$.growl.error({title: res.errors['login-login'], message: "Попробуйте, еще раз!!!", duration: 5000});
			}if (typeof res.errors['registration-email'] != 'undefined') {  
					$.growl.error({title: res.errors['registration-email'], message: "Попробуйте, еще раз!!!", duration: 5000});
			}  
		
		},
		error: function(){
			$.growl.error({title: 'Ошибка', message: "Произошла ошибка на стороне сервера!", duration: 5000});
		}
	});      
	return false;
});
JS
);	
?>	