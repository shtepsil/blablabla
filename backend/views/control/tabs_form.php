<?php
/**
 * @var common\models\Structure $item
 * @var
 */

use common\components\Debugger as d;
use backend\widgets\PagesForm\PagesForm;

$this->title = ($item->name ? $item->name : 'Добавить город');
?>
<?= $this->render('//blocks/breadcrumb') ?>
<?if(ADMIN_FORM_DEBUG_RES){echo d::res();}?>
<section id="content">
    <?= PagesForm::widget(['item' => $item, 'template' => 'form/tabs']) ?>
</section>
