<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $items \common\models\Reviews[]
 *
 */
use frontend\form\ReviewSend;
use frontend\widgets\ActiveField;
use frontend\widgets\ActiveForm;
use shadow\widgets\ReCaptcha\ReCaptcha;
use yii\helpers\Html;
use yii\helpers\Url;

$context = $this->context;
?>
    <div class="inner_page_1">
        <div class="responses_page">
            <?= $this->render('//blocks/breadcrumbs') ?>
            <a class="response_button" href="#" id="open_review">Оставить отзыв</a>
            <div class="response_form">
                <?php
                $model = new ReviewSend();
                if (!Yii::$app->user->isGuest) {
                    $model->name = Yii::$app->user->identity->username;
                }
                $model->rate = 5;
                $name_model = $model->formName();
                $form = ActiveForm::begin([
                    'action' => Url::to(['site/send-form', 'f' => 'review_send']),
                    'enableAjaxValidation' => false,
                    'options' => ['enctype' => 'multipart/form-data'],
                    'fieldClass' => ActiveField::className(),
                    'fieldConfig' => [
                        'options' => ['class' => 'response_line'],
//        'template' => "{label}<div class=\"col-md-10\">{input}\n{error}</div>",
                        'template' => <<<HTML
<div class="response_label">{label}</div>
<div class="response_input">
	<div class="input_wrapper">
		{input}
	</div>
</div>
<div class="clear"></div>
HTML
                        ,
                    ],
                    'events' => [
                        'ajaxComplete' => <<<JS
function(data){
    $.each(recaptchaInstances,function(){
        grecaptcha.reset(this);
    })
}
JS
                        ,
                    ]
                ]); ?>
                <?= Html::activeHiddenInput($model, 'rate') ?>
                <h2>Оставить отзыв</h2>
                <a class="response_close" href="#"></a>
                <?= $form->field($model, 'name'); ?>
                <div class="response_line">
                    <div class="response_label">Оценка</div>
                    <div class="response_form_rate">
                        <ul class="rating" id="reviewRate">
                            <li class="on"></li>
                            <li class="on"></li>
                            <li class="on"></li>
                            <li class="on"></li>
                            <li class="on"></li>
                        </ul>
                    </div>
                    <div class="clear"></div>
                </div>
                <?= $form->field($model, 'plus_body')->textarea(); ?>
                <?= $form->field($model, 'minus_body')->textarea(); ?>
                <?= $form->field($model, 'body')->textarea(); ?>
                <?= $form->field($model, 'verifyCode', ['template' => '{input}<div class="help-block error_all"></div>', 'options' => ['class' => 'form_captcha']])->widget(ReCaptcha::className()) ?>
                <input type="submit" value="Отправить">
                <?php ActiveForm::end(); ?>
            </div>
            <div class="main_title">
                <h1>Отзывы пользователей</h1>
            </div>
            <div class="responses_list">
                <?php foreach ($items as $item): ?>
                    <article>
                        <div class="response_top">
                            <h2><?= $item->name ?></h2>
                            <span><?= Yii::$app->formatter->asDate($item->created_at, 'd MMMM Y'); ?></span>
                        </div>
                        <div class="response_rate">
                            <ul class="rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?= Html::tag('li', '', ['class' => (($i <= $item->rate) ? 'on' : '')]) ?>
                                <?php endfor; ?>
                            </ul>
                        </div>
                        <div class="response_content">
                            <ul>
                                <?php if ($item->plus_body): ?>
                                    <li><span>Достоинства:</span><span><?= $item->plus_body ?></span></li>
                                <?php endif; ?>
                                <?php if ($item->minus_body): ?>
                                    <li><span>Недостатки:</span><span><?= $item->minus_body ?></span></li>
                                <?php endif; ?>
                                <li><span>Комментарий:</span><span><?= $item->body ?></span></li>
                            </ul>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="clear"></div>
        </div>
        <?= $this->render('//blocks/basket') ?>
    </div>
<?php
$this->registerJs(<<<JS
$('.response_close').on('click', function (e) {
    e.preventDefault();
    $('.response_form').hide()
});
$('#open_review').on('click', function (e) {
    e.preventDefault();
    $('.response_form').show()
})
var defaul_rate = 4;
$('#reviewRate').on('click','li', function (e) {
    var index = $('li', '#reviewRate').index(this);
    defaul_rate = index;
    $('li', '#reviewRate').each(function(){
        if($(this).index()<=index){
            $(this).addClass('on');
        }else{
            $(this).removeClass('on');

        }
    });
    $('#{$name_model}-rate').val(index+1);
}).on('mouseover','li',function(e){
    var index = $('li', '#reviewRate').index(this);
    $('li', '#reviewRate').each(function(){
        if($(this).index()<=index){
            $(this).addClass('on');
        }else{
            $(this).removeClass('on');

        }
    })
}).on('mouseleave',function(e){
    var index = defaul_rate;
    $('li', '#reviewRate').each(function(){
        if($(this).index()<=index){
            $(this).addClass('on');
        }else{
            $(this).removeClass('on');

        }
    })
})
JS
)
?>