<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $items \common\models\Jobs[]
 *
 */
use frontend\form\JobsSend;
use frontend\widgets\ActiveField;
use frontend\widgets\ActiveForm;
use shadow\widgets\ReCaptcha\ReCaptcha;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$context = $this->context;
?>
<div class="inner_page_1">
    <div class="text_page">
        <?= $this->render('//blocks/breadcrumbs') ?>
        <section class="text_content">
            <div class="main_title">
                <h1>Вакансии</h1>
            </div>
            <div class="vacancy_button">
                <a href="#add_jobs">Оставить резюме</a>
            </div>
            <div class="vacancy_list">
                <?php foreach($items as $item): ?>
                    <article>
                        <div class="vacancy_top">
                            <h2><?=$item->name?></h2>
                            <span><?= Yii::$app->formatter->asDate($item->created_at, 'd MMMM Y'); ?></span>
                        </div>
                        <div class="vacancy_content">
                            <p><?=$item->body?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <section class="vacancy_add" id="add_jobs">
                <div class="main_title always_small">
                    <h2>Оставить резюме</h2>
                </div>
                <div class="vacancy_form">
                    <?php
                    $model = new JobsSend();
                    $name_model = $model->formName();
                    $form = ActiveForm::begin([
                        'action' => Url::to(['site/send-form', 'f' => 'jobs_send']),
                        'enableAjaxValidation' => false,
                        'options' => ['enctype' => 'multipart/form-data'],
                        'fieldClass' => ActiveField::className(),
                        'fieldConfig' => [
                            'required'=>false,
                            'options' => ['class' => 'vacancy_line'],
                            'template' => <<<HTML
<div class="vacancy_label">{label}</div>
<div class="vacancy_input">
	<div class="input_wrapper">
		{input}
	</div>
</div>
<div class="clear"></div>
HTML
                            ,
                        ],
                        'events'=>[
                            'ajaxComplete'=> <<<JS
function(data){
    $.each(recaptchaInstances,function(){
        grecaptcha.reset(this);
    })
}
JS
                            ,
                        ]
                    ]); ?>
                    <h3>Оставить резюме</h3>
                    <?= $form->field($model, 'name',['inputOptions'=>['autocomplete'=>"name"]]); ?>
                    <?= $form->field($model, 'dob'); ?>
                    <?= $form->field($model, 'phone',['inputOptions'=>['autocomplete'=>"tel"]])->widget(\yii\widgets\MaskedInput::className(), [
                        'mask' => '+7(999)-999-9999',
                        'definitions' =>[
                            'maskSymbol'=>'_'
                        ],
                        'options'=>[
                            'class'=>''
                        ]
                    ]); ?>
                    <?= $form->field($model, 'email',['inputOptions'=>['autocomplete'=>"email"]]); ?>
                    <?= $form->field($model, 'country',['inputOptions'=>['autocomplete'=>"address-level2"]]); ?>
                    <?= $form->field($model, 'citizenship'); ?>
                    <?= $form->field($model, 'education'); ?>
                    <?= $form->field($model, 'position'); ?>
                    <?= $form->field($model, 'resume',[
                        'template' => <<<HTML
<div class="vacancy_label">Прирепить файл</div>
<div class="vacancy_input">
	<div class="input_wrapper">
	    <a class="form_button" href="#" id="change_resume">Обзор</a>
		{input}
		<div class="help-block"></div>
	</div>
</div>
<div class="clear"></div>
HTML
                    ])->fileInput(['style'=>'display:none']); ?>
                    <?= $form->field($model, 'verifyCode', ['template' => '{input}<div class="help-block error_all"></div>', 'options' => ['class' => 'form_captcha']])->widget(ReCaptcha::className()) ?>
                    <input type="submit" value="Отправить">
                    <?php ActiveForm::end(); ?>
                </div>
            </section>
        </section>
        <div class="clear"></div>
    </div>
    <?= $this->render('//blocks/basket') ?>
</div>
<?php
$this->registerJs(<<<JS
$('#{$name_model}-resume').change(function () {
var file=$(this)[0].files;
    $('#change_resume').html(file[0].name);
});
$('#change_resume').on('click',function(e){
e.preventDefault();
    $('#{$name_model}-resume').trigger('click');
})
JS
)
?>