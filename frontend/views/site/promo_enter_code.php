<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $code string
 */

use frontend\form\PromoEnterCode;
use frontend\form\PromoRegistration;
use frontend\form\RegistrationPage;
use frontend\widgets\ActiveField;
use frontend\widgets\ActiveForm;
use yii\helpers\Url;

$context = $this->context;
$model = new PromoEnterCode();
?>
<section class="AllCont padSpace">
    <h1 class="title">Активация кода</h1>
    <?php $form = ActiveForm::begin([
        'action' => Url::to(['site/send-code', 'code' => $code]),
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
    <?= $form->field($model, 'sms_code', ['inputOptions' => ['autocomplete' => "off"]]); ?>

    <div class="string">
        <button class="btn_Form blue" type="submit">Активировать</button>
    </div>
    <? ActiveForm::end(); ?>
</section>
