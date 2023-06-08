<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 */
use frontend\form\RegistrationPage;
use frontend\widgets\ActiveField;
use frontend\widgets\ActiveForm;
use yii\helpers\Url;

$context = $this->context;
$model = new RegistrationPage();
?>
<div class="breadcrumbsWrapper padSpace">
    <?= $this->render('//blocks/breadcrumbs') ?>
</div>
<section class="AllCont padSpace">
    <h1 class="title">Регистрация</h1>
    <?php $form = ActiveForm::begin([
        'action' => Url::to(['site/send-form', 'f' => 'registration_page']),
        'enableAjaxValidation' => false,
        'options' => ['enctype' => 'multipart/form-data', 'class' => 'recoveryPass'],
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
    <?= $form->field($model, 'email', [
        'inputOptions' => ['autocomplete' => "email"],
    ]); ?>
    <div class="string">
        <button class="btn_Form blue" type="submit">Зарегистрироваться</button>
    </div>
    <? ActiveForm::end(); ?>
</section>
