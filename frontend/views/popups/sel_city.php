<?
use common\components\Debugger as d;
use yii\helpers\Url;

?>
<ul class="switchCity">
    <? foreach ($context->function_system->data_city_view as $key => $city): ?>
        <li>
            <a href="<?= Url::to(['site/index', 'city' => $key]) ?>"><?= $city ?></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?= Url::to(['site/delivery', 'id' => $key]) ?>" class="delivery-info">
                <div class="what-is-delivery" title="Узнать условия доставки99<br>по городу  <?= $city ?>">!</div>
            </a>
        </li>
    <? endforeach; ?>
</ul>