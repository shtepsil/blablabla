<?php

use common\components\Debugger as d;
use yii\helpers\Url;
use yii\web\View;

$context = $this->context;

$tabs = require __DIR__ . '/tabs.php';

?>
<?= Yii::$app->controller->renderPartial('/blocks/debug/style', ['tabs' => $tabs]) ?>
<div class="TextContent padSpace wrap-debug">
    <h1 class="title">Debug</h1>
    <div class="textInterface">
        <div class="tabs">
            <? foreach ($tabs as $tab_index => $tab): ?>
                <div id="content-<?= $tab_index ?>">
                    <?= $this->renderAjax('//blocks/debug/' . $tab['path'], [
                        'tab_index' => $tab_index,
                        'context' => $context
                    ]) ?>
                </div>
            <? endforeach ?>
            <div class="tabs__links">
                <? foreach ($tabs as $tab_index => $tab): ?>
                    <a href="#content-<?= $tab_index ?>"><?= $tab_index . ' ' . $tab['name'] ?></a>
                <? endforeach ?>
            </div>

        </div>
    </div>

</div>
<?
$url = Url::to(['site/tab-debug-ajax']);
$this->registerJs(<<<JS
function tabsAjax(tab, Data, stop){
    $('.tab' + tab + '-buttons [class*=btn]').on('click', function() {
        var tthis = $(this),
        res = $('.res-tab' + tab),
        wrap = $('.wrap-debug'),
        load = wrap.find('.tab' + tab + '-buttons img.loading'),
        form_elements = tthis.parent().find('input, textarea'),
        textarea = tthis.parent().find('textarea'),
        name = tthis.attr('name'),
        url = '{$url}?a=',
        action = 'debug',
        method = 'post';

        if(Data === undefined){ Data = {}; }

        // : Настройка action =======================
        // Если action передан со страницы
        if(Data['action'] !== undefined){
            action = Data['action'];
        }
        // Если action передан с кнопки
        if(tthis.attr('data-action') !== undefined){
            action = tthis.attr('data-action');
        }
        // : /настройка action =======================

        // : Получение полей формы =========
        Data['type'] = tthis.attr('name');

        if(form_elements.length > 0){
            if(form_elements.length > 1){
                Data['inputs'] = form_elements.serializeArray();
            }else{
                switch(form_elements.attr('type')){
                    case 'checkbox':
                        Data[form_elements.attr('name')] = form_elements.prop('checked');
                        break;
                    case 'text':
                        Data[form_elements.attr('name')] = form_elements.val();
                        break;
                    default:
                        Data[form_elements.attr('name')] = form_elements.val();

                }
            }
        }
        // : /поля формы ===================

        res.html('result' + tab);

        /*
		$.growl.error({title: 'Ошибка', message: 'Всем привет я error', duration: 5000});
		$.growl.notice({title: 'Ошибка', message: 'Всем привет я notice', duration: 5000});
		$.growl.warning({title: 'Ошибка', message: 'Всем привет я warning', duration: 5000});
		*/

        // var csrf_param = $('meta[name=csrf-param]').attr('content');
        // var csrf_token = $('meta[name=csrf-token]').attr('content');
        // Data[csrf_param] = csrf_token;

        url += action;
        Data['request'] = {
            url: url,
            method: method,
            a: action
        };
        cl(Data);
        if(stop !== undefined) return;

        $.ajax({
            url: url,
            method: method,
            dataType: 'json',
            cache: false,
            data: Data,
            beforeSend: function(){ load.fadeIn(100); }
        }).done(function(data){
            res.html('Done<br><pre>' + prettyPrintJson.toHtml(data) + '</pre>');
        }).fail(function(data){
            res.html('Fail<br>' + JSON.stringify(data));
        }).always(function(){
            load.fadeOut(100);
        });
    });
}

JS
    , View::POS_BEGIN
)
?>