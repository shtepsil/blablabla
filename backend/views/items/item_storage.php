<?php
/**
 *
 * @var \yii\web\View $this
 * @var $context \shadow\widgets\AdminForm
 * @var $citys City[]
 * @var $item_count \common\models\ItemsCount[]
 * @var $item \common\models\Items
 * @var string $name
 */
use common\models\City;
use yii\helpers\Html;
$name = 'itemsCount';
$citys = City::find()->orderBy(['name'=>SORT_ASC])->all();
if ($item->isNewRecord) {
    $item_count = [];
} else {
    $item_count = $item->getItemsCounts()->indexBy('city_id')->all();
}
?>
<div class="col-md-5 table-primary">
    <table class="table table-striped table-hover">
        <colgroup>
            <col>
            <col  width="250px">
        </colgroup>
        <thead>
        <tr>
            <th>Город</th>
            <th>Количество</th>
        </tr>
        </thead>
        <tbody id="items-<?= $name ?>">
        <?php foreach ($citys as $city): ?>
            <?
            $value = '';
            if(isset($item_count[$city->id])){
                $value = $item_count[$city->id]->count;
            }
            ?>
            <tr>
                <td >
                    <?=$city->name?>
                </td>
                <td class="name">
                    <?= Html::textInput($name . '[' . $city->id . '][count]',$value, ['class'=>'form-control']) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>