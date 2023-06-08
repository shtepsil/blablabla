<?php
/**
 * @var $context \frontend\controllers\SiteController
 * @var $this \yii\web\View
 * @var $content string
 * @var $items backend\modules\catalog\models\Category[]
 */

$context = $this->context;
?>
<? foreach($items as $item): ?>
<div class="cat_block">
    <a class="set__search__result" href="<?=$item->url() ?>"> 
        <span class="__name text_search_cat name_cat_search"><?=$item->name?>
			<span><?php 
				if (!$item->isHideincatalog) {
					echo Yii::t('main', 'count_items', ['n' => $item->countItem()]);
				}
			 ?>
			</span>
        </span><br>
    </a>
	</div>
<? endforeach; ?>