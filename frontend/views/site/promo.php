<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $code string
 */

use frontend\form\PromoRegistration;
use frontend\form\RegistrationPage;
use frontend\widgets\ActiveField;
use frontend\widgets\ActiveForm;
use yii\helpers\Url;

$context = $this->context;
$model = new PromoRegistration();
?>
<section class="AllCont padSpace">
    <h1 class="title">Активация кода</h1>
    <?php $form = ActiveForm::begin([
        'action' => Url::to(['site/send-promo', 'code' => $code]),
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
    <?= $form->field($model, 'phone', ['inputOptions' => ['autocomplete' => "tel"]])->widget(\yii\widgets\MaskedInput::className(), [
        'mask' => '+7(999)-999-9999',
        'definitions' =>[
            'maskSymbol'=>'_'
        ],
        'options'=>[
            'class'=>''
        ]
    ]); ?>
    <div class="string">
        <button class="btn_Form blue" type="submit">Активировать</button>
    </div>
    <? ActiveForm::end(); ?>
</section>
