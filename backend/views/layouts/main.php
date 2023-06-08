<?php

use common\components\Debugger as d;
use backend\assets\AdminAsset;
use backend\assets\IeAsset;
use backend\components\widgets\Menu;
use shadow\assets\AngularAssets;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */
AdminAsset::register($this);
IeAsset::register($this);
//TODO Пока не будем использовать angular
//AngularAssets::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" ng-app="app">
<!--[if IE 8]>
<html class="ie8" lang="<?= Yii::$app->language ?>" ng-app="app"> <![endif]-->
<!--[if IE 9]>
<html class="ie9 gt-ie8" lang="<?= Yii::$app->language ?>" ng-app="app"> <![endif]-->
<!--[if gt IE 9]>
<html class="gt-ie8 gt-ie9 not-ie" lang="<?= Yii::$app->language ?>" ng-app="app"> <!--<![endif]-->
<head>
    <meta charset="<?= Yii::$app->charset ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <? if (!\Yii::$app->params['devicedetect']['isDesktop']): ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <? endif ?>
    <meta name="robots" content="none" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <? if (false): ?>
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,600,700,300&subset=latin" rel="stylesheet" type="text/css">
    <? endif ?>
    <?php $this->head() ?>
</head>
<body class="theme-default main-menu-fixed">
<?php $this->beginBody() ?>
<script>var init = [];</script>
<div id="main-wrapper">
    <div id="main-navbar" class="navbar navbar-inverse" role="navigation">
        <button type="button" id="main-menu-toggle"><i class="navbar-icon fa fa-bars icon"></i><span class="hide-menu-text">HIDE MENU</span></button>
        <div class="navbar-inner">
            <div class="navbar-header">
                <a href="/admin" class="navbar-brand">
                    <div>
                        <img alt="Instinct" src="/admin/images/logo.png">
                    </div>
                </a>
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-navbar-collapse"><i class="navbar-icon fa fa-bars"></i></button>
            </div>
            <div id="main-navbar-collapse" class="collapse navbar-collapse main-navbar-collapse">
                <div>
                    <div class="right clearfix">
                        <ul class="nav navbar-nav pull-right right-navbar-nav">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle user-menu" data-toggle="dropdown">
                                    <? if (false): ?>
                                        <img src="/admin/assets/demo/avatars/1.jpg" alt="">
                                    <? endif ?>
                                    <span><?= Yii::$app->user->identity->username ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <? if (false && in_array(Yii::$app->user->identity->role, ['manager', 'collector', 'driver', 'kassir'])): ?>
                                        <li>
                                            <a href="<?= Url::to(['kassa/index']) ?>"><i class="dropdown-icon fa fa-money"></i>&nbsp;&nbsp;Касса</a>
                                        </li>
                                    <? endif ?>
                                    <li>
                                        <a href="<?= Url::to(['s-users/control', 'id' => Yii::$app->user->id]) ?>"><i class="dropdown-icon fa fa-cog"></i>&nbsp;&nbsp;Настройки</a>
                                    </li>
                                    <? if (false): ?>
                                        <li>
                                            <a href="#"><span class="label label-warning pull-right">New</span>Profile</a>
                                        </li>
                                        <li>
                                            <a href="#"><span class="badge badge-primary pull-right">New</span>Account</a>
                                        </li>
                                        <li>
                                            <a href="#"><i class="dropdown-icon fa fa-cog"></i>&nbsp;&nbsp;Settings</a>
                                        </li>
                                    <? endif ?>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="<?= Url::to(['site/logout']) ?>"><i class="dropdown-icon fa fa-power-off"></i>&nbsp;&nbsp;Выход</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="main-menu" role="navigation">
        <div id="main-menu-inner">
            <?
            $admin_menu = Yii::$app->params['admin_menu'];
            ?>

            <?= Menu::widget([
                'items' => $admin_menu,
                'linkTemplate' => '<a href="{url}"><i class="menu-icon fa {icon}"></i><span class="mm-text">{label}</span></a>',
                'linkTemplateWithoutIcon' => '<a href="{url}"><span class="mm-text">{label}</span></a>',
                'subLinkTemplate' => '<a tabindex="-1" href="{url}"><i class="menu-icon fa {icon}"></i><span class="mm-text">{label}</span></a>',
                'subLinkTemplateWithoutIcon' => '<a tabindex="-1" href="{url}"><span class="mm-text">{label}</span></a>',
                'submenuTemplate' => "\n<ul>\n{items}\n</ul>\n",
                'options' => [
                    'class' => 'navigation'
                ]
            ]) ?>
            <? if (false): ?>
                <div class="menu-content">
                    <a href="<?= Url::to(['module/add']) ?>" class="btn btn-primary btn-block btn-outline dark">Создать модуль</a>
                </div>
            <? endif ?>
        </div>
    </div>
    <div id="main-menu-bg"></div>
    <div id="content-wrapper">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            'options' => [
                'class' => 'breadcrumb-page breadcrumb'
            ]
        ]) ?>
        <?= $content ?>
        <? if (false): ?>
            <footer class="margin-sm-vr">
                <div class="panel no-margin-b">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8 text-muted">
                                © 2015 - <?= date('Y') ?>
                                <a href="/" target="_blank">CMS</a>
                                &nbsp;&nbsp;─&nbsp;&nbsp;
                                Работает на
                                <a href="http://www.yiiframework.com/" target="_blank">Yii</a> <?= Yii::getVersion() ?>
                                &nbsp;&nbsp;─&nbsp;&nbsp;
                                Тема
                                <a href="https://wrapbootstrap.com/theme/pixeladmin-premium-admin-theme-WB07403R9" target="_blank">PixelAdmin</a>
                            </div>
                            <? if (false): ?>
                                <div class="col-md-4 text-right hidden-sm hidden-xs text-muted">
                                    Спасибо, за использование
                                    <a href="/">CMS</a>
                                </div>
                            <? endif ?>
                        </div>
                    </div>
                </div>
            </footer>
        <? endif ?>
    </div>
</div>
<?php if (false): ?>
    <!-- Get jQuery from Google CDN -->
    <!--[if !IE]> -->
    <script type="text/javascript"> window.jQuery || document.write('<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js">' + "<" + "/script>"); </script>
    <!-- <![endif]-->
    <!--[if lte IE 9]>
    <script type="text/javascript"> window.jQuery || document.write('<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js">' + "<" + "/script>"); </script>
    <![endif]-->
<?php endif; ?>
<style>
    @-webkit-keyframes ld {
        0% {
            transform: rotate(0deg) scale(1);
        }
        50% {
            transform: rotate(180deg) scale(1.1);
        }
        100% {
            transform: rotate(360deg) scale(1);
        }
    }

    @-moz-keyframes ld {
        0% {
            transform: rotate(0deg) scale(1);
        }
        50% {
            transform: rotate(180deg) scale(1.1);
        }
        100% {
            transform: rotate(360deg) scale(1);
        }
    }

    @-o-keyframes ld {
        0% {
            transform: rotate(0deg) scale(1);
        }
        50% {
            transform: rotate(180deg) scale(1.1);
        }
        100% {
            transform: rotate(360deg) scale(1);
        }
    }

    @keyframes ld {
        0% {
            transform: rotate(0deg) scale(1);
        }
        50% {
            transform: rotate(180deg) scale(1.1);
        }
        100% {
            transform: rotate(360deg) scale(1);
        }
    }

    .loading-progress {
        position: relative;
        opacity: .8;
        color: transparent !important;
        text-shadow: none !important;
    }

    .loading-progress:hover,
    .loading-progress:active,
    .loading-progress:focus {
        cursor: default;
        color: transparent;
        outline: none !important;
        box-shadow: none;
    }

    .loading-progress:before {
        content: '';
        display: inline-block;
        position: absolute;
        background: transparent;
        border: 1px solid #fff;
        border-top-color: transparent;
        border-bottom-color: transparent;
        border-radius: 50%;
        box-sizing: border-box;
        top: 50%;
        left: 50%;
        margin-top: -12px;
        margin-left: -12px;

        width: 24px;
        height: 24px;

        -webkit-animation: ld 1s ease-in-out infinite;
        -moz-animation: ld 1s ease-in-out infinite;
        -o-animation: ld 1s ease-in-out infinite;
        animation: ld 1s ease-in-out infinite;
    }
</style>
<? $this->registerJs(<<<JS
//JS
init.push(function () {
    // Javascript code here
})
bootbox.setLocale('ru');
$(document).on('keyup keypress','form', function(e) {
    var code = e.keyCode || e.which;
    if (code == 13) {
        e.preventDefault();
        return false;
    }
});
$(document).on('click', '.btn-confirm', function (e) {
    e.preventDefault();
	var res = $('.res');
    var url = $(this).attr('href');
    bootbox.confirm({
        message: "Вы уверены что хотите удалить?",
        callback: function (result) {
            if (result) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'JSON',
                    success: function (data) {
						res.html('Done<br><pre>' + prettyPrintJson.toHtml(data) + '</pre>');
                        if (typeof data.error != 'undefined') {
                            $.growl.error({title: 'Ошибка', message: data.error, duration: 5000});
                        }
                        if (typeof data.success != 'undefined') {
                            //$.growl.notice({title: 'Успех', message: message.success});
                            window.location.reload();
                        }
                    },
                    error: function (data) {
						res.html('Fail<br>' + JSON.stringify(data));
                        $.growl.error({title: 'Ошибка', message: "Произошла ошибка на стороне сервера!", duration: 5000});
                    }
                })

            }
        },
        className: "bootbox-sm"
    });
});
window.PixelAdmin.start(init, {main_menu: {detect_active: false, store_state: true}});
JS
    , $this::POS_END, 'main_js'); ?>
	
<?php

$this->registerJs(<<<JS

$("form.saves").on('beforeSubmit', function(){  

var url = $(this).attr('action');

	var data = $(this).serialize();
	$.ajax({
		url: url,
		type: 'POST', 
		data: data,
		success: function(res){ 
		
			console.log(res);
			
			if (res.js) {
				eval(res.js);
			}
		
			if (res.message['success']) {
				$.growl.notice({title: 'Успех', message: res.message['success']});
			//	window.location.href = '/';
			}else if (res.message['error']) {  
					$.growl.error({title: res.message['error'], message: "Что-то пошло не так!!!", duration: 5000});
			} 
		
		},
		error: function(res){
			
			console.log(res);
			$.growl.error({title: 'Ошибка', message: "Произошла ошибка на стороне сервера!", duration: 5000});
		}
	});      
	return false;
});
JS
);	

?>		
	
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
