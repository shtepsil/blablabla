<?php
/**
 * @var $this \yii\web\View
 */

use common\components\Debugger as d;
use shadow\helpers\SArrayHelper;
use shadow\widgets\AdminActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?= $this->render('//blocks/breadcrumb') ?>
<?if(ADMIN_FORM_DEBUG_RES) echo d::res();?>
<section id="content">
    <div id="pageEdit">
        <?php $form = AdminActiveForm::begin([
            'action' => '',
            'enableAjaxValidation' => false,
            'options' => ['enctype' => 'multipart/form-data'],
            'fieldConfig' => [
                'options' => ['class' => 'form-group simple'],
                'template' => "{label}<div class=\"col-md-3 col-xs-5\">{input}\n{error}</div>",
                'labelOptions' => ['class' => 'col-md-2 col-xs-2 control-label'],
            ],
        ]); ?>
        <div style="position: relative;">
            <div class="form-actions panel-footer" style="padding-left: 0px;padding-top: 0px;">
                <?= Html::submitButton('<i class="fa fa-download "></i> Сгенерировать каталог', ['class' => 'btn-success btn-save btn-lg btn', 'data-hotkeys' => 'ctrl+s', 'name' => 'continue']) ?>
            </div>
        </div>
        <div class="panel form-horizontal">
            <div class="panel-heading">
                <?php if(!empty($lastSync)) { ?>
                <p>Последнее обновление каталога проводилось: <?= $lastSync ?></p>  
                <?php } ?>
            </div>
            <hr class="no-margin-vr" />
        </div>
        <?php AdminActiveForm::end(); ?>
    </div>
</section>
<? $this->registerJs(<<<JS
$('.styled-finputs-example').pixelFileInput({ placeholder: 'Выберите файл' });
JS
)?>

