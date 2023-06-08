<?php

use common\components\Debugger as d;
use yii\web\View;

$this->title = 'Admin Debugs';
$context = $this->context;
$tabs = require __DIR__ . '/tabs.php';

?>
<style>
    <?= $this->renderAjax('//blocks/debug/style.css') ?>
</style>

<h3><?= $this->title ?></h3>
<br>
<div class="wrap-debug">
    <ul class="nav nav-tabs">
        <? foreach ($tabs as $tab_index => $tab): ?>
            <li<?= (DEBUG_TAB_ACTIVE == $tab_index) ? ' class="active"' : '' ?>>
                <a href="#<?= $tab['path'] ?>-content" data-toggle="tab"><?= $tab_index ?>) <?= $tab['name'] ?></a>
                </li>
            <? endforeach ?>
    </ul>

    <div class="tab-content">
        <? foreach ($tabs as $tab_index => $tab): ?>
            <div id="<?= $tab['path'] ?>-content" class="tab-pane fade <?= (DEBUG_TAB_ACTIVE == $tab_index) ? 'in active' : '' ?>">
                <!--                <h3>--><? //=$tab['name']?><!--</h3>-->
                <div class="tab-content">
                    <?= $this->renderAjax('//blocks/debug/' . $tab['path'], [
                        'tab_index' => $tab_index,
                        'context' => $context
                    ]) ?>
                </div>
            </div>
        <? endforeach ?>
    </div>
</div>

<?
$this->registerJs(<<<JS
//JS
function tabsAjax(tab, Data, stop){
    $('.tab' + tab + '-buttons [class*=btn]').on('click',function(){
        var tthis = $(this),
        res = $('.res-tab' + tab),
        wrap = $('.wrap-debug'),
        load = wrap.find('.tab' + tab + '-buttons img.loading'),
        form_elements = tthis.parent().find('input, textarea'),
        textarea = tthis.parent().find('textarea'),
        name = tthis.attr('name'),
        url = 'tab-debug-ajax',
        action = 'debug',
        method = 'post';

        if(tthis.attr('data-ajax') == 'false'){
            return;
        }

        if(Data === undefined){ Data = {}; }

        // Если action передан со страницы
        if(Data['action'] !== undefined){
            action = Data['action'];
        }

        // Если action передан с кнопки
        if(tthis.attr('data-url') !== undefined){
            action = tthis.attr('data-action');
            // method = 'get';
            // Просто для показа в консоли
            Data['method'] = method;
        }

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

        res.html('result' + tab);

        if(url === undefined || url === ''){
            $.growl.warning({title: 'Ошибка', message: 'Не передан url', duration: 5000});
            return;
        }

        /*
		$.growl.error({title: 'Ошибка', message: 'Всем привет я error', duration: 5000});
		$.growl.notice({title: 'Ошибка', message: 'Всем привет я notice', duration: 5000});
		$.growl.warning({title: 'Ошибка', message: 'Всем привет я warning', duration: 5000});
		*/

        // var csrf_param = $('meta[name=csrf-param]').attr('content');
        // var csrf_token = $('meta[name=csrf-token]').attr('content');
        // Data[csrf_param] = csrf_token;

        Data['url'] = url + '?a=' + action;
        cl(Data);
        if(stop !== undefined) return;

        $.ajax({
            url: '/admin/' + url + '?a=' + action,
            type: method,
            dataType: 'json',
            cache: false,
            data: Data,
            beforeSend: function(){ load.fadeIn(100); }
        }).done(function(data){
            res.html('Done<br><pre>' + prettyPrintJson.toHtml(data) + '</pre>');
        }).fail(function(data){
            res.html('Fail<br>' + JSON.stringify(data));
            // res.html('Fail<br><pre>' + prettyPrintJson.toHtml(data) + '</pre>');
        }).always(function(){
            load.fadeOut(100);
        });
    });
}

JS
    , View::POS_BEGIN
)
    ?>