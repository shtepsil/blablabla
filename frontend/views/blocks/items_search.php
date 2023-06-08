<?php
/**
 * @var $context \frontend\controllers\SiteController
 * @var $this \yii\web\View
 * @var $content string
 * @var $items backend\modules\catalog\models\Items[]
 */
 use yii\helpers\Html;
$context = $this->context;

?>
<? foreach($items as $item): ?>
<div class="img_item_margin">
    <a class="img_item_search goods__block__mini" href="<?=$item->url() ?>">
<table border="1">
<tr>
<td class="vertical-align: top;"><?php echo Html::img($item->img(), ['style' => 'width:50px;height:50px;object-fit:contain']) ?></td>
<td style="vertical-align: top;"><div style="margin-top:16px" class="__name text_search name_item_search"><?=$item->name?></div></td>
<td style="vertical-align: top;"><div style="margin-top:16px" class="price_item_search">
<?php echo $item->real_price() ?> Т      
</div></td>
</tr>
</table>	
</a>
<div>
<hr>
<? endforeach; ?>
