<?php
/**
 *
 * @var $this \yii\web\View
 * @var $item \common\models\Sets
 * @var $context \shadow\widgets\AdminForm
 * @var array $filters
 * @var string $name
 */
use common\models\Items;
use shadow\assets\Select2Assets;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

?>
<?php
Select2Assets::register($this);
$this->registerJs(<<<JS
$('.widget-select2-no').select2({
    width: '250px',
    language: 'ru'
});
JS
);
$name = 'SetsItems';
$context = $this->context;
$q = new ActiveQuery(Items::className());
if (!$item->isNewRecord) {
    $q->andWhere(['<>', 'id', $item->id]);
}
$db_items = $q->all();
$data = ArrayHelper::map($db_items, 'id', 'name');
$items = [];
if(!$item->isNewRecord){
    $items = $item->setsItems;
}
?>
    <div class="row">
        <div class="table-primary col-md-7">
            <table class="table table-striped table-hover">
                <colgroup>
                    <col width="250px">
                    <col width="250px">
                    <col width="150px">
                    <col width="150px">
                    <col>
                </colgroup>
                <thead>
                <tr>
                    <th>Товар</th>
                    <th>Цена</th>
                    <th>Цена в сете</th>
                    <th>Количество в сете</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody id="items-<?= $name ?>">
                <?php foreach ($items as $sets_item): ?>
                    <?=$this->render('sets_item',['item'=>$sets_item->item,'price'=>$sets_item->price,'count'=>$sets_item->count])?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row col-sx-5">
        <?= Html::dropDownList($name . 'Clone[new][item_id]', '', $data, ['class' => 'form-control widget-select2-no', 'id'=>"add_id_$name"]) ?>
        <a href="#" class="btn btn-primary" id="<?= $name ?>_add">Добавить</a>
    </div>
<?php
$add_url=Json::encode(Url::to(['sets/add-item']));
$this->registerJs(<<<JS
var name_{$name} = '{$name}[{new_index}][{value}]';
var index_{$name} = 0;
$('#{$name}_add').on('click',function(e) {
  e.preventDefault();
  var id=$('#add_id_{$name}').val();
  $.ajax({
  url:$add_url,
      type:'GET',
      data:{id:id},
      dataType:'HTML',
      success:function(data){
          $('option:selected','#add_id_{$name}').prop('disabled',true);
          var data_option= $('option:selected','#add_id_{$name}').data('data');
          data_option.disabled=true;
          $('option:selected','#add_id_{$name}').data('data',data_option);
          $('#items-{$name}').append(data);
          $.growl.notice({title: 'Успех', message: 'Добавлен новый товар'});
      },
      error:function(){
          $.growl.error({title: 'Ошибка', message: "Произошла ошибка на стороне сервера!", duration: 5000});
      }
  })
});
$('#items-{$name}').on('click', '.deleted-{$name}>a', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    $('option[value=' + id + ']', '#add_id_{$name}').prop('disabled', false);
    var data_option = $('option[value=' + id + ']', '#add_id_{$name}').data('data');
    data_option.disabled = false;
    $('option[value=' + id + ']', '#add_id_{$name}').data('data', data_option);
    $(this).parents('tr').remove();
})
JS
);
?>