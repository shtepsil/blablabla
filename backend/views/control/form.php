<?php
/**
 * @var common\models\Structure $item
 * @var
 */
 
use common\components\Debugger as d;
use shadow\widgets\AdminForm;

?>
<?= $this->render('//blocks/breadcrumb') ?>
<?if(ADMIN_FORM_DEBUG_RES){echo d::res();}?>
<section id="content">
    <?php

    /*
     * Блокировку ставим только на модель - items
     */
    if(preg_match('/items/i',Yii::$app->request->pathInfo)){

        /*
         * Слой, ограничивающий доступ ко всей форме.
         * Управляющий фактор доступа - роль пользователя.
         * Разрешенные роли находятся в массиве 'edit_fields'
         */
        if(!in_array(Yii::$app->user->getIdentity()->role,Yii::$app->params['edit_fields'])):
    //    if(true):
    ?>
        <div class="role-overlay-content">
            <div class="content-text">
                Для редактирования вам доступны только некоторые поля.
            </div>
        </div>
    <?endif?>
    <?}?>
    <?= AdminForm::widget(['item' => $item]) ?>
</section>