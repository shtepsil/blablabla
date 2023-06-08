<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $recipe common\models\Recipes
 * @var $recipeItems common\models\RecipesItem[]
 * @var $item common\models\Items
 */
use common\models\Recipes;
use frontend\widgets\ActiveField;
//use frontend\widgets\ActiveForm;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$context = $this->context;
//$model = new Recovery();
$recipe=$context->recipe;
$recipeItems = $recipe->getRecipesItems()->with(['recipesItemAlts.item'=>function($q){
    $q->where(['isVisible'=>1]);
},'item'])->all();
?>
<div id="popup_6">
    <div class="popup_content">
        <div class="popup_basket_top">
            <div class="popup_basket_title_2">ДОБАВЛЕНИЕ ИНГРЕДИЕНТОВ РЕЦЕПТА В КОРЗИНУ</div>
        </div>
        <div class="popup_basket_content_wrapper">
            <div class="popup_basket_content">
                <div class="popup_basket_table">
                    <div class="popup_basket_list">
                        <ol>
                            <?php foreach($recipeItems as $recipeItem): ?>
                                <?php
                                $item = $recipeItem->item;
                                if(!$item->isVisible&&$recipeItem->recipesItemAlts){
                                    $item = $recipeItem->recipesItemAlts[0]->item;
                                }
                                $count=number_format($recipeItem->item_count, 1,'.','')
                                ?>
                                <li>
                                    <div class="item_1">
                                        <input type="hidden" name="items[<?=$item->id?>]" value="<?=$count?>" data-type="<?= $item->measure ?>" data-id="<?=$item->id?>" data-price="<?=$item->price?>">
                                        <div class="popup_basket_image">
                                            <a href="<?= Url::to(['site/item', 'id' => $item->id]) ?>">
                                                <img alt="" src="<?=$item->img(false)?>">
                                            </a>
                                        </div>
                                        <div class="popup_basket_name">
                                            <a href="<?= Url::to(['site/item', 'id' => $item->id]) ?>">Ингредиент: <?=$recipeItem->name?> - <?=$recipeItem->count?><br>Продукт: <?=$item->name?></a>
                                        </div>
                                    </div>
                                    <div class="item_4">
                                        <span><?=round($item->price*$count)?> тнг</span>
                                        <a class="popup_basket_close" href="#"></a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="popup_basket_bottom">
            <input type="submit" value="Добавить в корзину" id="send_recipe_items">
        </div>
    </div>
</div>
<?php
$url_cart=Url::to(['site/cart']);
$url_redirect=Url::to(['site/order']);
$this->registerJs(<<<JS
$('#popup_6').on('click', '.popup_basket_close', function (e) {
        e.preventDefault();
        $(this).parents('li').remove();
    }).on('click', '#send_recipe_items', function (e) {
        e.preventDefault();
        var data = $('input[data-price]', '#popup_6').serializeArray();
        data.push({name: 'action', value: 'addMulti'});
        $.ajax({
            url: '{$url_cart}',
            type: 'GET',
            dataType: 'JSON',
            data: data,
            success: function (data) {
                window.location.reload()
            },
            error: function () {

            }
        });
    })
JS
)
?>

