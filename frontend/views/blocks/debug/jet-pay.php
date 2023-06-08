<?php

use common\components\Debugger as d;
use frontend\form\CallbackSend;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\widgets\ActiveField;
$model = new CallbackSend();
?>
<style>

</style>
<div class="row">
    <div class="col-md-12">
        <div class="tab<?=$tab_index?>-buttons" style="position: relative;">
            <?=Html::img($context->AppAsset->baseUrl . '/images/animate/loading.gif', [
                'class' => 'loading'
            ])?>
            <div class="form-gorup">
                <h3>Файл debug.txt</h3>
                <div class="mini-form">
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
                </div>
            </div>
            <br>
            <div class="form-gorup">
                <h3>Тест</h3>
                <div class="mini-form">
                    <button name="btn_push" class="btn_debug error">Нажать</button>
                    &nbsp;&nbsp;&nbsp;
                </div>
            </div>
            <br>
        </div>
        <?=d::res(false, 'res-tab' . $tab_index);?>
    </div>
</div>
<br><br>
<?php
$action = 'jet-pay';
$this->registerJs(<<<JS
//JS
$(function(){});
var params = {};
params['action'] = '{$action}';
tabsAjax('{$tab_index}', params);
JS
)
?>
