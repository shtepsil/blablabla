<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\UserController
 */

use common\components\Debugger as d;
use frontend\widgets\ActiveField;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Html;

$context = $this->context;

?>
<div class="breadcrumbsWrapper padSpace">
    <?= $this->render('//blocks/breadcrumbs') ?>
</div>
<div class="Cabinet padSpace">
    <h1 class="gTitle">Мои реквизиты</h1>
    <div class="wrap-form-requisites">
        <div class="block-layer-requisites <?=$block_layer?>"></div>
        <? $form = ActiveForm::begin([
            'action' => Url::to(['user/send-form', 'f' => 'edit_requisites']),
            'enableAjaxValidation' => false,
            'options' => [
                'enctype' => 'multipart/form-data',
                'class' => 'formProfile formRequisites',
            ],
            'fieldClass' => ActiveField::className(),
            'fieldConfig' => [
                'required' => false,
                'options' => ['class' => 'string'],
                'template' => <<<HTML
    {label}
    {input}
HTML
                ,
            ],
        ]); ?>

        <?= $form->field($model, 'entity_name'); ?>
        <?= $form->field($model, 'entity_address'); ?>
        <?= $form->field($model, 'entity_bin'); ?>
        <?= $form->field($model, 'entity_iik'); ?>
        <?= $form->field($model, 'entity_bank'); ?>
        <?= $form->field($model, 'entity_bik'); ?>
        <?= $form->field($model, 'entity_nds', [
            'template' => '{input}{label}'
        ])->checkbox([],false)?>

        <div class="string">
            <button class="btn_Form blue" type="submit">Сохранить</button>
        </div>
        <? ActiveForm::end(); ?>
    </div><!-- /wrap-form-requisites -->

    <? goto user_requisites; ?>
    <?=Html::input('checkbox', 'set_user_requisites')?>
    <lable>Открепить оптовика</lable>
    <? user_requisites: ?>

<?=(FRONTEND)?d::res():''?>

</div>
<?php
$this->registerJs(<<<JS

$('.wrap-form-requisites .block-layer-requisites').on('click', function(){
    
    $.growl.warning({
        title: 'Внимание',
        message: 'Вы прикреплены к реквизитам этого пользователя.<br>Их редактировать нельзя.',
        duration: 7000
    });
    
});

JS
)

?>