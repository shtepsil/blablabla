<?php
/**
 * @var $this yii\web\View
 * @var $items common\models\City[]
 * @var $context backend\controllers\CityController
 */
use common\components\Debugger as d;
use yii\helpers\Url;
use yii\bootstrap\Html;

$context = $this->context;
$url = $context->id;
?>
<?= $this->render('//blocks/breadcrumb') ?>
<section id="content" class="cities-list">
    <div class="panel">
        <div class="panel-heading">
            <a href="<?= Url::to([$url . '/control']) ?>" class="btn-primary btn">
                <i class="fa fa-plus"></i> <span class="hidden-xs hidden-sm">Добавить</span></a>
        </div>
        <div class="panel-body">
            <table class="table-primary table table-striped table-hover">
                <colgroup>
                    <col width="25px">
                    <col>
                    <col>
                    <col>
                    <col width="25px">
                </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Название</th>
                        <th class="text-right">Видимость</th>
                        <th class="text-right">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr id="layout_normal">
                            <th>
                                <?= $item->id ?>
                            </th>
                            <th>
                                <a href="<?= Url::to([$url . '/control', 'id' => $item->id]) ?>">
                                    <?= $item->name ?>
                                </a>
                            </th>
                            <td class="text-right cities-view-btns">
                                <div class="wrap-btns">
                                    <? if ($item->id != 1): ?>
                                        <div href="<?= Url::to([$url . '/change-view']) ?>" title="Включить видимость"
                                            class="btn btn-danger btn-xs no-link <?= ($item->view == '0') ? '' : 'dn' ?>"
                                            data-id="<?= $item->id ?>" data-view="1">
                                            <i class="fa fa-eye-slash"></i>
                                        </div>
                                        <div href="<?= Url::to([$url . '/change-view']) ?>" title="Выключить видимость"
                                            class="btn btn-success btn-xs no-link <?= ($item->view == '1') ? '' : 'dn' ?>"
                                            data-id="<?= $item->id ?>" data-view="0">
                                            <i class="fa fa-eye"></i>
                                        </div>
                                        <?= Html::img('@web/images/animate/loading.gif', ['class' => 'loading']); ?>
                                    <? else: ?>
                                        <div class="no-change-city"><i class="fa fa-eye"></i></div>
                                    <? endif ?>
                                </div>
                            </td>
                            <td class="actions text-right">
                                <a href="<?= Url::to([$url . '/control', 'id' => $item->id]) ?>"
                                    class="btn-success btn-xs btn">
                                    <i class="fa fa-pencil fa-inverse"></i>
                                </a>
                                <a href="<?= Url::to([$url . '/deleted', 'id' => $item->id]) ?>"
                                    class="btn-danger btn-xs btn-confirm btn">
                                    <i class="fa fa-times fa-inverse"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <? if(BACKEND) echo d::res() ?>
        </div>
    </div>
</section>
<?php

$this->registerJs(<<<JS

$('.cities-list .wrap-btns div:not(.no-change-city)').on('click', function(){
    var tthis = $(this),
        res = $('.res'),
        load = tthis.parent().find('img.loading'),
        Data = {};

    res.html('result');

    Data['id'] = tthis.attr('data-id');
    Data['view'] = tthis.attr('data-view');

    // data-view=1 - это кнопка отключить (красная)
    if(tthis.attr('data-view') == '1'){
        var btnView = tthis.parent().find('[data-view=0]');
        var viewMessage = 'Видимость города включена';
    }else{
        var btnView = tthis.parent().find('[data-view=1]');
        var viewMessage = 'Видимость города отключёна';
    }

    // cl(Data);
    // return;

    $.ajax({
        url: tthis.attr('href'),
        type: 'post',
        dataType: 'json',
        cache: false,
        data: Data,
        beforeSend: function(){
            load.fadeIn(100);
        }
    }).done(function(data){
        res.html('Done<br><pre>' + prettyPrintJson.toHtml(data) + '</pre>');
        if(data.status == 200){
            $.growl.notice({title: 'Успех', message: viewMessage, duration: 5000});
            tthis.hide(10, function(){
                btnView.show();
            });
        }else{
            $.growl.error({title: 'Внимание', message: 'Статус не изменён', duration: 5000});
        }
    }).fail(function(data){
        $.growl.error({title: 'Внимание', message: 'Не известная ошибка', duration: 5000});
    }).always(function(){
        load.fadeOut(100);
    });
});

JS
);

?>