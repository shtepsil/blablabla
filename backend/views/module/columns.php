<?php
/**
 * Created by PhpStorm.
 * Project: yii2-cms
 * User: lxShaDoWxl
 * Date: 08.05.15
 * Time: 12:36
 *
 * @var \yii\web\View $this
 * @var \common\models\Module $item
 */
use common\models\Columns;
use yii\helpers\Url;

$this->registerJs(
    <<<JS
    (function(){
$('#add-field').on('click',function(e){
    $('.has-error','#fields_add').removeClass('has-error');
    $('.help-block','#fields_add').html('');
    e.preventDefault();
    var form_field=$('#fields_add');
    //$('input,select',form_field).serializeArray();
    $.ajax({
        url:$(this).attr('href'),
        method:'POST',
        dataType:'JSON',
        data:$('input,select',form_field).serializeArray(),
        success:function(data_return){
                if (typeof data_return.errors !='undefined') {
                    var msgs = data_return.errors;
                    if (msgs !== null && typeof msgs === 'object') {
                        $.each(msgs, function (i,obj) {
                            $('.field-'+i).addClass('has-error');
                            $('.help-block','.field-'+i).html(obj[0])
                        });
                    }
                }else{
                 if( typeof data_return.message !='undefined'){
                    var message = data_return.message;
                    if(typeof message.error!='undefined'){
                        $.growl.error({title:'Ошибка', message: message.error ,duration:5000});
                    }
                    if(typeof message.success!='undefined'){
                        $('input,select',form_field).each(function(){
                            var default_v=$(this).data('default');
                            console.log(default_v);
                            if(typeof default_v !='undefined'){
                                $(this).val(default_v);
                            }else{
                                if($(this).is('input')){
                                    $(this).val('')
                                }
                                if($(this).is('select')){
                                    $(this).val($('option',this).eq(0).attr('value'))
                                }
                            }
                        });
                        $.growl.notice({title:'Успех', message: message.success });
                    }
                }
                if(typeof data_return.column!='undefined'){
                        $('#all_fields').append(data_return.column)
                }
            }
        },
        error: function () {
            $.growl.error({title:'Ошибка', message: "Произошла ошибка на стороне сервера!",duration:5000 });
        }
    })
});
$('#remove-fields').on('click',function(e){
    e.preventDefault();
    $('.check_deleted:checked').each(function() {
        $('#field-'+$(this).val()).remove();
    })
})
})();
JS
);
$default = Columns::findAll(['isDefault' => 1, 'module_id' => NULL]);
if($item->isNewRecord){
    $columns = [];
}else{
    $columns=Columns::find()->where(['module_id'=>$item->id])->orderBy(['order'=>'asc'])->all();
}
$columns = array_merge($default, $columns);
?>
<table id="section-fields" class="table table-primary table-striped table-hover">
    <colgroup>
        <col width="30px">
        <col width="50px">
        <col width="100px">
        <col width="200px">
        <col width="100px">
        <col width="150px">
    </colgroup>
    <thead>
    <tr>
        <td></td>
        <td>Порядок</td>
        <td>Ключ</td>
        <td>Название</td>
        <td>Тип поля</td>
        <td>Показывать в списке</td>
    </tr>
    </thead>
    <tbody id="all_fields">
        <?php
        foreach ($columns as $column) {
           echo $this->context->renderPartial('_column', ['item' => $column]);
        }

        ?>
    </tbody>
</table>
<div class="panel-footer">
    <div class="btn-group">
        <button id="remove-fields" class="btn-danger btn"><i class="fa fa-trash-o"></i> Удалить выделенные</button>
    </div>
</div>
<div class="panel-group panel-group-success" id="accordion-success-example">
    <div class="panel">
        <div class="panel-heading">
            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion-success-example" href="#fields_add">
                Создать новое поле
            </a>
        </div>
        <!-- / .panel-heading -->
        <div id="fields_add" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="form-group simple field-columns-name required ">
                    <label class="col-md-2 control-label" for="columns-name">Название</label>
                    <div class="col-md-10">
                        <input type="text" id="columns-name" class="form-control" name="field[name]" value="" placeholder="Название">
                        <? if (!$item->isNewRecord): ?>
                            <input type="hidden" name="field[module_id]" value="<?= $item->id ?>">
                        <? endif ?>
                        <p class="help-block help-block-error"></p>
                    </div>
                </div>
                <div class="form-group simple field-columns-orders required ">
                    <label class="col-md-2 control-label" for="columns-order">Порядок</label>
                    <div class="col-md-1">
                        <input type="number" id="columns-order" class="form-control" name="field[order]" value="0" data-default="0" placeholder="Порядок">
                        <p class="help-block help-block-error"></p>
                    </div>
                </div>
                <div class="form-group simple field-columns-key required ">
                    <label class="col-md-2 control-label" for="columns-key">Ключ</label>

                    <div class="col-md-10">
                        <input type="text" id="columns-key" class="form-control" name="field[key]" value="" placeholder="Ключ">
                        <p class="help-block help-block-error"></p>
                    </div>
                </div>
                <div class="form-group simple field-columns-type required ">
                    <label class="col-md-2 control-label" for="columns-type">Тип поля</label>

                    <div class="col-md-10">
                        <select name="field[type]" id="columns-type" class="form-control">
                            <option value="string">Строка</option>
                            <option value="html_text">HTML текст</option>
                            <option value="text">Простой текст</option>
                        </select>
                        <p class="help-block help-block-error"></p>
                    </div>
                </div>
                <div class="btn-group">
                    <a id="add-field" class="btn-success btn" href="<?= Url::to(['module/add_column']) ?>">
                        <i class="fa fa-plus-square"></i> Добавить
                    </a>
                </div>
            </div>
            <!-- / .panel-body -->
        </div>
        <!-- / .collapse -->
    </div>
    <!-- / .panel -->
</div>
