<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 */
use common\components\Debugger as d;
use yii\helpers\Url;
use common\components\helpers\CHtml;
use frontend\components\MicroData;

$context = $this->context;
$end = end($context->breadcrumbs);
$md = new MicroData();

?>
<?php if ($context->breadcrumbs): ?>
    <ul <?=$md->get('breadcrumbs','itemscope')?> class="breadcrumbs">
        <?php $itemprop_position = 1; foreach ($context->breadcrumbs as $breadcrumb): ?>
            <li <?=$md->get('breadcrumbs','itemlist')?>>
                <?php if ($end == $breadcrumb): ?>
                    <span <?=$md->get('breadcrumbs','propLabel')?>><?= $breadcrumb['label'] ?></span>
                    <?=$md->getMetaProp('position',$itemprop_position);?>
                <?php else: ?>
                    <a <?=$md->get('breadcrumbs','propLink')?> href="<?= Url::to($breadcrumb['url']) ?>">
                        <span <?=$md->get('breadcrumbs','propLabel')?>><?= $breadcrumb['label'] ?></span>
                        <?=$md->getMetaProp('name',$breadcrumb['label'])?>
                    </a>
                    <?=$md->getMetaProp('position',$itemprop_position);?>
                <?php endif; ?>
            </li>
            <?php $itemprop_position++; endforeach; ?>
    </ul>
<?php endif; ?>

