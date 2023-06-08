<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 */

use common\components\Debugger as d;
use frontend\form\Login;
use frontend\form\SmsLogin;
use frontend\form\CodeLogin;
use frontend\form\Registration;
use frontend\widgets\ActiveField;
//use frontend\widgets\ActiveForm;
use yii\widgets\ActiveForm;
use shadow\widgets\SAuthChoice;
use yii\helpers\Url;

$context = $this->context;
?>
<div class="tabInterface">
    <ul class="tabHead" data-tab="head">
        <li class="current" style="width:33.33%">
            <!--<div class=""><span>Вход</span></div>-->
            <p class="title" style="font-size:13px">Вход</p>
            <div class="description">Для зарегистрированных пользователей</div>
        </li>
        <li data-popup="recheck" style="width:33.33%">
            <!--<div class=""><span>Регистрация</span></div>-->
            <p class="title" style="font-size:13px">Регистрация</p>
            <div class="description">Для новых <br>пользователей</div>
        </li>
        <li data-popup="recheck" style="width:33.33%; text-align:left">
            <p class="title" style="font-size:13px">Sms - вход</p>
            <div class="description">Введите номер <br>телефона</div>
        </li>
    </ul>
    <ul class="tabBody" data-tab="body">
        <li class="current">
            <?
            $model = new Login();
            ?>
            <?php $form = ActiveForm::begin([
                'action' => Url::to(['site/send-form', 'f' => 'login']),
                'enableAjaxValidation' => false,
                'options' => ['enctype' => 'multipart/form-data', 'class' => 'formPopupEnter save'],
                'fieldClass' => ActiveField::className(),
                'fieldConfig' => [
                    'options' => ['class' => 'string'],
                    'template' => <<<HTML
{label}
{input}
HTML
                    ,
                ]
            ]); ?>
            <?= $form->field($model, 'login', ['inputOptions' => ['autocomplete' => "email"]]); ?>
            <?= $form->field($model, 'password')->passwordInput(); ?>
            <div class="string twoCol">
                <div class="col">
                    <button class="btn_Form blue" type="submit">Войти</button>
                </div>
                <div class="col">
                    <a href="<?= Url::to(['site/recovery-password']) ?>" class="btnLink">Забыли пароль?</a>
                </div>
            </div>
            <div class="string">
                <label>Войти с помощью</label>
                <?= SAuthChoice::widget([
                    'baseAuthUrl' => ['site/auth'],
                    'popupMode' => true,
                    'options' => [
                        'class' => 'formSocial'
                    ]
                ]) ?>
                <? if (false): ?>
                    <ul class="formSocial">
                        <li class="facebook">
                            <a href="#"></a>
                        </li>
                        <li class="twitter">
                            <a href="#"></a>
                        </li>
                        <li class="vkontakte">
                            <a href="#"></a>
                        </li>
                    </ul>
                <? endif ?>
            </div>
<?if(AUTH_REG_RES) echo d::res()?>
            <? ActiveForm::end(); ?>
        </li>
        <li>
            <?
            $model = new Registration();
            ?>
            <?php $form = ActiveForm::begin([
                'action' => Url::to(['site/send-form', 'f' => 'registration']),
                'enableAjaxValidation' => false,
                'options' => ['enctype' => 'multipart/form-data', 'class' => 'formPopupRegister save'],
                'fieldClass' => ActiveField::className(),
                'fieldConfig' => [
                    'options' => ['class' => 'string'],
                    'template' => <<<HTML
{label}
{input}
HTML
                    ,
                ],
            ]); ?>
            <?= $form->field($model, 'name', ['inputOptions' => ['autocomplete' => "name"]]); ?>
            <?= $form->field($model, 'phone', ['inputOptions' => ['autocomplete' => "tel"]])->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '+7(999)-999-9999',
                'definitions' =>[
                    'maskSymbol'=>'_'
                ],
                'options'=>[
                    'class'=>''
                ]
            ]); ?>
            <?= $form->field($model, 'email', [
                'inputOptions' => ['autocomplete' => "email"],
                'template' => <<<HTML
{label}
<div class="input_inform">
{input}
<span>Будет являться логином для входа в Личный кабинет</span>
</div>
HTML
            ]); ?>
            <?= $form->field($model, 'password')->passwordInput(); ?>
            <?= $form->field($model, 'verifyCode', [
                'options' => [
                    'class' => 'string captcha'
                ]
            ])->widget(\yii\captcha\Captcha::className(), [
                'options' => [
                    'class' => ''
                ],
                'template' => <<<HTML
<div class="captcha">
{input}
<div class="image">
{image}
</div>
<a href="#" class="changeImage">Показать другую картинку</a>
</div>

HTML
            ]) ?>
            <div class="string">
                <button class="btn_Form blue" type="submit">Зарегистрироваться</button>
            </div>
<?if(AUTH_REG_RES) echo d::res()?>
            <? ActiveForm::end(); ?>
        </li>
        <li>
         <?
            $model = new SmsLogin();
            ?>
            <?php $form = ActiveForm::begin([
                'id' => 'smsform',
                'action' => Url::to(['site/send-form', 'f' => 'sms_login']),
                'enableAjaxValidation' => false,
                'options' => ['enctype' => 'multipart/form-data', 'class' => 'formPopupEnter save'],
                'fieldClass' => ActiveField::className(),
                'fieldConfig' => [
                    'options' => ['class' => 'string'],
                    'template' => <<<HTML
{label}
{input}
HTML
                    ,
                ]
            ]); ?>

            <?=$form->field($model, 'phone', ['inputOptions' => ['autocomplete' => "tel"]])->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '+7(999)-999-9999',
                'definitions' =>[
                    'maskSymbol'=>'_'
                ],
                'options'=>[
                    'class'=>'',
                    'id'=>'input'
                ]
            ]);
            ?>
            <div class="string twoCol" id="resmslogin">
                <div class="col">
                    <button class="btn_Form blue" type="submit"><p id="getsms">Получить sms</p><p style="display:none" id="resmsnew">Отправить повторно sms</p></button>
                </div>
            </div>
            <? ActiveForm::end(); ?>
            <?
            $model = new CodeLogin();
            ?>
            <div id="countdown">
                <div id="tiles"></div>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'codeform',
                'action' => Url::to(['site/send-form', 'f' => 'code_login']),
                'enableAjaxValidation' => false,
                'options' => ['enctype' => 'multipart/form-data', 'class' => 'formPopupEnter  save', 'style' => 'display:none'],
                'fieldClass' => ActiveField::className(),
                'fieldConfig' => [
                    'options' => ['class' => 'string'],
                    'template' => <<<HTML
{label}
{input}
HTML
                    ,
                ]
            ]); ?>
            <?= $form->field($model, 'code', [
                'inputOptions' => [
                    'data-vv-validate-on' => "keyup|blur",
                    'maxlength' => 4,
                    'type' => "number",
                    'aria-required' => "true",
                    'aria-invalid' => "true",
                    'placeholder' => "Введите код",
                ]
            ]); ?>
            <div class="string twoCol">
                <div class="col">
                    <button class="btn_Form blue" type="submit">Вход</button>
                </div>
            </div>
            <? ActiveForm::end(); ?>
<?if(AUTH_REG_RES) echo d::res()?>
        </li>
    </ul>
</div>
<?
$this->registerJs(<<<JS
$('.formPopupRegister').on('click','.changeImage',function(e){
e.preventDefault();
$('#registration-verifycode-image').trigger('click.yiiCaptcha')
})
JS
)
?>

<?php
$this->registerJs(<<<JS

$("form.save").on('beforeSubmit', function(){  

var url = $(this).attr('action');

	var data = $(this).serialize();
	var d_res = $('.res');
	$.ajax({
		url: url,
		type: 'POST', 
		data: data,
		success: function(res){
		    d_res.html(prettyPrintJson.toHtml(res));
			if (res.js) {
				eval(res.js);
			} else if (typeof res.message != 'undefined') {
				$.growl.notice({title: 'Успех', message: res.message['success']});
				window.location.href = '/';
			}else if (typeof res.errors['login-login'] != 'undefined') {  
                $.growl.error({title: res.errors['login-login'], message: "Попробуйте, еще раз!!!", duration: 5000});
			}
			if (typeof res.errors['registration-email'] != 'undefined') {  
                $.growl.error({title: res.errors['registration-email'], message: "Попробуйте, еще раз!!!", duration: 5000});
			}
			if (typeof res.errors['registration-phone'] != 'undefined') {
                $.growl.error({title: res.errors['registration-phone'], message: "Попробуйте, еще раз!!!", duration: 5000});
			}  
		
		},
		error: function(res){
		    d_res.html('Fail' + JSON.stringify(res));
			$.growl.error({title: 'Ошибка', message: "Произошла ошибка на стороне сервера!", duration: 5000});
		}
	});      
	return false;
});
JS
);	
//registration-verifycode
?>	


