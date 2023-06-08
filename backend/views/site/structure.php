<?php
/* @var $this yii\web\View */

use backend\assets\StructureAsset;
use shadow\widgets\STree;
use yii\helpers\Url;

$this->title = 'Структура сайта';
//$this->params['breadcrumbs'][] = $this->title;
StructureAsset::register($this);
?>
<style type="text/css"></style>
<section id="content">
	<div id="page-tree" class="panel">
		<div class="panel-heading">
			<a href="<?=Url::to(['structure/add'])?>" class="btn-default btn" data-hotkeys="ctrl+a"><i class="fa fa-plus"></i>
				<span class="hidden-xs hidden-sm">Добавить страницу</span></a>
			<button id="pageMapReorderButton" class="btn-primary btn-sm btn" data-hotkeys="ctrl+s">
				<i class="fa fa-sort"></i> Сортировать
			</button>
			<div class="panel-heading-controls hidden-xs hidden-sm">
				<form action="/page/search" method="post" accept-charset="utf-8" class="form-inline form-search">
					<input type="hidden" name="token" value="7enjisyN0fRynMaPrQTmaVMe923Z+wybD+kZX4u/Yno=">
					<div class="input-group input-group-sm">
						<input type="text" id="page-seacrh-input" name="search" class="form-control no-margin-b" placeholder="Найти страницу">

						<div class="input-group-btn">
							<button class="btn-default btn"><i class="fa fa-search"></i> Поиск</button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<table id="page-tree-header" class="table table-primary">
			<thead>
			<tr class="row">
				<th class="col-xs-7">Страница</th>
				<th class="col-xs-2 text-right">Дата</th>
				<th class="col-xs-2 text-right">Статус</th>
				<th class="col-xs-1 text-right">Действия</th>
			</tr>
			</thead>
		</table>
		<ul id="page-tree-list" class="tree-items list-unstyled" data-level="0">
			<li data-id="1">
				<div class="tree-item">
					<div class="title col-xs-7">
						<a href="<?=Url::to(['structure/edit','id'=>1])?>"><i class="fa fa-home fa-lg fa-fw"></i> Главная</a>
						<a href="<?=Yii::$app->urlManagerFrontEnd->createUrl(['site/index'])?>" class="item-preview" target="_blank">
							<span class="label label-info"><i class="fa fa-globe"></i> Просмотреть страницу</span></a>
					</div>
					<div class="actions col-xs-offset-4 col-xs-1 text-right">
						<a href="<?=Url::to(['structure/add','parent'=>1])?>" class="btn-default btn-xs btn"><i class="fa fa-plus"></i></a>
					</div>
					<div class="clearfix"></div>
				</div>
                <?=STree::widget($params)?>
                <? if (false): ?>
                    <ul data-level="1" class="list-unstyled">
                        <li data-id="10" class="item-expanded">
                            <div class="tree-item">
                                <div class="title col-xs-7">
                                    <i class="fa fa-fw item-expander item-expander-expand fa-plus"></i>
                                    <a href="/backend/page/edit/10"><i class="fa fa-file-o fa-fw"></i> About</a>
                                    <span class="label label-info">Редирект: /about/us.html</span>
                                    <a href="http://demo.kodicms.ru/about.html" class="item-preview" target="_blank">
                                        <span class="label label-info"><i class="fa fa-globe"></i> Просмотреть страницу</span>
                                    </a>
                                </div>
                                <div class="date col-xs-2 text-right text-muted">
                                    07.09.2014
                                </div>
                                <div class="status col-xs-2 text-right">
                                    <span class="label label-success editable-status editable editable-click" data-value="100">Опубликована</span>
                                </div>
                                <div class="actions col-xs-1 text-right">
                                    <div class="btn-group">
                                        <a href="/backend/page/add/10" class="btn-default btn-xs btn">
                                            <i class="fa fa-plus"></i></a>
                                        <a href="/backend/page/delete/10" class="btn-xs btn-confirm btn-danger btn">
                                            <i class="fa fa-times fa-inverse"></i></a>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <ul data-level="2" class="list-unstyled">
                                <li data-id="11">
                                    <div class="tree-item">
                                        <div class="title col-xs-7">
                                            <a href="/backend/page/edit/11" data-icon="file-o fa-fw">Terms of Service
                                            </a>
                                            <a href="http://demo.kodicms.ru/about/terms-of-service.html" class="item-preview" target="_blank">
                                                <span class="label label-info"><i class="fa fa-globe"></i> Просмотреть страницу</span>
                                            </a>
                                        </div>
                                        <div class="date col-xs-2 text-right text-muted">
                                            07.09.2014
                                        </div>
                                        <div class="status col-xs-2 text-right">
                                            <span class="label label-success editable-status" data-value="100">Опубликована</span>
                                        </div>
                                        <div class="actions col-xs-1 text-right">
                                            <div class="btn-group">
                                                <a href="/backend/page/add/11" class="btn-default btn-xs btn">
                                                    <i class="fa fa-plus"></i></a>
                                                <a href="/backend/page/delete/11" class="btn-xs btn-confirm btn-danger btn">
                                                    <i class="fa fa-times fa-inverse"></i></a>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </li>
                                <li data-id="4">
                                    <div class="tree-item">
                                        <div class="title col-xs-7">
                                            <a href="/backend/page/edit/4" data-icon="file-o fa-fw">Privacy Policy</a>
                                            <a href="http://demo.kodicms.ru/about/privacy-policy.html" class="item-preview" target="_blank">
                                                <span class="label label-info"><i class="fa fa-globe"></i> Просмотреть страницу</span>
                                            </a>
                                        </div>
                                        <div class="date col-xs-2 text-right text-muted">
                                            06.09.2014
                                        </div>
                                        <div class="status col-xs-2 text-right">
                                            <span class="label label-success editable-status" data-value="100">Опубликована</span>
                                        </div>
                                        <div class="actions col-xs-1 text-right">
                                            <div class="btn-group">
                                                <a href="/backend/page/add/4" class="btn-default btn-xs btn">
                                                    <i class="fa fa-plus"></i></a>
                                                <a href="/backend/page/delete/4" class="btn-xs btn-confirm btn-danger btn">
                                                    <i class="fa fa-times fa-inverse"></i></a>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </li>
                                <li data-id="14">
                                    <div class="tree-item">
                                        <div class="title col-xs-7">
                                            <a href="/backend/page/edit/14" data-icon="file-o fa-fw">About us</a>
                                            <a href="http://demo.kodicms.ru/about/us.html" class="item-preview" target="_blank">
                                                <span class="label label-info"><i class="fa fa-globe"></i> Просмотреть страницу</span>
                                            </a>
                                        </div>
                                        <div class="date col-xs-2 text-right text-muted">
                                            08.09.2014
                                        </div>
                                        <div class="status col-xs-2 text-right">
                                            <span class="label label-success editable-status" data-value="100">Опубликована</span>
                                        </div>
                                        <div class="actions col-xs-1 text-right">
                                            <div class="btn-group">
                                                <a href="/backend/page/add/14" class="btn-default btn-xs btn">
                                                    <i class="fa fa-plus"></i></a>
                                                <a href="/backend/page/delete/14" class="btn-xs btn-confirm btn-danger btn">
                                                    <i class="fa fa-times fa-inverse"></i></a>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </li>
                                <li data-id="18">
                                    <div class="tree-item">
                                        <div class="title col-xs-7">
                                            <a href="/backend/page/edit/18" data-icon="file-o fa-fw">Protected page</a>
                                            <a href="http://demo.kodicms.ru/about/protected-page.html" class="item-preview" target="_blank">
                                                <span class="label label-info"><i class="fa fa-globe"></i> Просмотреть страницу</span>
                                            </a>
                                        </div>
                                        <div class="date col-xs-2 text-right text-muted">
                                            11.09.2014
                                        </div>
                                        <div class="status col-xs-2 text-right">
                                            <span class="label label-warning editable-status" data-value="200">Защищена паролем</span>
                                        </div>
                                        <div class="actions col-xs-1 text-right">
                                            <div class="btn-group">
                                                <a href="/backend/page/add/18" class="btn-default btn-xs btn">
                                                    <i class="fa fa-plus"></i></a>
                                                <a href="/backend/page/delete/18" class="btn-xs btn-confirm btn-danger btn">
                                                    <i class="fa fa-times fa-inverse"></i></a>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <li data-id="13">
                            <div class="tree-item">
                                <div class="title col-xs-7">
                                    <a href="/backend/page/edit/13"><i class="fa fa-file-o fa-fw"></i> Our clients</a>
                                    <a href="http://demo.kodicms.ru/our-clients.html" class="item-preview" target="_blank">
                                        <span class="label label-info"><i class="fa fa-globe"></i> Просмотреть страницу</span>
                                    </a>
                                </div>
                                <div class="date col-xs-2 text-right text-muted">
                                    08.09.2014
                                </div>
                                <div class="status col-xs-2 text-right">
                                    <span class="label label-success editable-status editable editable-click" data-value="100">Опубликована</span>
                                </div>
                                <div class="actions col-xs-1 text-right">
                                    <div class="btn-group">
                                        <a href="/backend/page/add/13" class="btn-default btn-xs btn">
                                            <i class="fa fa-plus"></i></a>
                                        <a href="/backend/page/delete/13" class="btn-xs btn-confirm btn-danger btn">
                                            <i class="fa fa-times fa-inverse"></i></a>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </li>
                        <li data-id="5" class="item-expanded">
                            <div class="tree-item">
                                <div class="title col-xs-7">
                                    <i class="fa fa-fw item-expander fa-plus"></i>
                                    <a href="/backend/page/edit/5"><i class="fa fa-file-o fa-fw"></i> Blog</a>
                                    <span class="label label-default">Гибридные документы</span>
                                    <a href="http://demo.kodicms.ru/blog.html" class="item-preview" target="_blank">
                                        <span class="label label-info"><i class="fa fa-globe"></i> Просмотреть страницу</span>
                                    </a>
                                </div>
                                <div class="date col-xs-2 text-right text-muted">
                                    06.09.2014
                                </div>
                                <div class="status col-xs-2 text-right">
                                    <span class="label label-success editable-status editable editable-click" data-value="100">Опубликована</span>
                                </div>
                                <div class="actions col-xs-1 text-right">
                                    <div class="btn-group">
                                        <a href="/backend/page/add/5" class="btn-default btn-xs btn">
                                            <i class="fa fa-plus"></i></a>
                                        <a href="/backend/page/delete/5" class="btn-xs btn-confirm btn-danger btn">
                                            <i class="fa fa-times fa-inverse"></i></a>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <ul data-level="2" class="list-unstyled" style="display: none;">
                                <li data-id="6">
                                    <div class="tree-item">
                                        <div class="title col-xs-7">
                                            <a href="/backend/page/edit/6" data-icon="file-o fa-fw">
                                                {:document_header|Blog
                                                post}
                                            </a>
                                            <a href="http://demo.kodicms.ru/blog/item.html" class="item-preview" target="_blank">
                                                <span class="label label-info"><i class="fa fa-globe"></i> Просмотреть страницу</span>
                                            </a>
                                        </div>
                                        <div class="date col-xs-2 text-right text-muted">
                                            06.09.2014
                                        </div>
                                        <div class="status col-xs-2 text-right">
                                            <span class="label label-default editable-status" data-value="101">Скрыта</span>
                                        </div>
                                        <div class="actions col-xs-1 text-right">
                                            <div class="btn-group">
                                                <a href="/backend/page/add/6" class="btn-default btn-xs btn">
                                                    <i class="fa fa-plus"></i></a>
                                                <a href="/backend/page/delete/6" class="btn-xs btn-confirm btn-danger btn">
                                                    <i class="fa fa-times fa-inverse"></i></a>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <li data-id="7">
                            <div class="tree-item">
                                <div class="title col-xs-7">
                                    <a href="/backend/page/edit/7"><i class="fa fa-file-o fa-fw"></i> FAQ</a>
                                    <a href="http://demo.kodicms.ru/faq.html" class="item-preview" target="_blank">
                                        <span class="label label-info"><i class="fa fa-globe"></i> Просмотреть страницу</span>
                                    </a>
                                </div>
                                <div class="date col-xs-2 text-right text-muted">
                                    06.09.2014
                                </div>
                                <div class="status col-xs-2 text-right">
                                    <span class="label label-success editable-status editable editable-click" data-value="100">Опубликована</span>
                                </div>
                                <div class="actions col-xs-1 text-right">
                                    <div class="btn-group">
                                        <a href="/backend/page/add/7" class="btn-default btn-xs btn">
                                            <i class="fa fa-plus"></i></a>
                                        <a href="/backend/page/delete/7" class="btn-xs btn-confirm btn-danger btn">
                                            <i class="fa fa-times fa-inverse"></i></a>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </li>
                        <li data-id="19">
                            <div class="tree-item">
                                <div class="title col-xs-7">
                                    <a href="/backend/page/edit/19"><i class="fa fa-file-o fa-fw"></i> Forum</a>
                                    <a href="http://demo.kodicms.ru/forum.html" class="item-preview" target="_blank">
                                        <span class="label label-info"><i class="fa fa-globe"></i> Просмотреть страницу</span>
                                    </a>
                                </div>
                                <div class="date col-xs-2 text-right text-muted">
                                    16.10.2014
                                </div>
                                <div class="status col-xs-2 text-right">
                                    <span class="label label-success editable-status editable editable-click" data-value="100">Опубликована</span>
                                </div>
                                <div class="actions col-xs-1 text-right">
                                    <div class="btn-group">
                                        <a href="/backend/page/add/19" class="btn-default btn-xs btn">
                                            <i class="fa fa-plus"></i></a>
                                        <a href="/backend/page/delete/19" class="btn-xs btn-confirm btn-danger btn">
                                            <i class="fa fa-times fa-inverse"></i></a>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </li>
                        <li data-id="9">
                            <div class="tree-item">
                                <div class="title col-xs-7">
                                    <a href="/backend/page/edit/9"><i class="fa fa-file-o fa-fw"></i> Sitemap</a>
                                    <a href="http://demo.kodicms.ru/sitemap.html" class="item-preview" target="_blank">
                                        <span class="label label-info"><i class="fa fa-globe"></i> Просмотреть страницу</span>
                                    </a>
                                </div>
                                <div class="date col-xs-2 text-right text-muted">
                                    07.09.2014
                                </div>
                                <div class="status col-xs-2 text-right">
                                    <span class="label label-default editable-status editable editable-click" data-value="101">Скрыта</span>
                                </div>
                                <div class="actions col-xs-1 text-right">
                                    <div class="btn-group">
                                        <a href="/backend/page/add/9" class="btn-default btn-xs btn">
                                            <i class="fa fa-plus"></i></a>
                                        <a href="/backend/page/delete/9" class="btn-xs btn-confirm btn-danger btn">
                                            <i class="fa fa-times fa-inverse"></i></a>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </li>
                        <li data-id="8">
                            <div class="tree-item">
                                <div class="title col-xs-7">
                                    <a href="/backend/page/edit/8"><i class="fa fa-file-o fa-fw"></i> Contact Us</a>
                                    <a href="http://demo.kodicms.ru/contacts.html" class="item-preview" target="_blank">
                                        <span class="label label-info"><i class="fa fa-globe"></i> Просмотреть страницу</span>
                                    </a>
                                </div>
                                <div class="date col-xs-2 text-right text-muted">
                                    06.09.2014
                                </div>
                                <div class="status col-xs-2 text-right">
                                    <span class="label label-success editable-status editable editable-click" data-value="100">Опубликована</span>
                                </div>
                                <div class="actions col-xs-1 text-right">
                                    <div class="btn-group">
                                        <a href="/backend/page/add/8" class="btn-default btn-xs btn">
                                            <i class="fa fa-plus"></i></a>
                                        <a href="/backend/page/delete/8" class="btn-xs btn-confirm btn-danger btn">
                                            <i class="fa fa-times fa-inverse"></i></a>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </li>
                        <li data-id="2">
                            <div class="tree-item">
                                <div class="title col-xs-7">
                                    <a href="/backend/page/edit/2"><i class="fa fa-file-o fa-fw"></i> Page not found</a>
                                    <span class="label label-default">Страница не найдена</span>
                                    <a href="http://demo.kodicms.ru/page-not-found.html" class="item-preview" target="_blank">
                                        <span class="label label-info"><i class="fa fa-globe"></i> Просмотреть страницу</span>
                                    </a>
                                </div>
                                <div class="date col-xs-2 text-right text-muted">
                                    06.09.2014
                                </div>
                                <div class="status col-xs-2 text-right">
                                    <span class="label label-default editable-status editable editable-click" data-value="101">Скрыта</span>
                                </div>
                                <div class="actions col-xs-1 text-right">
                                    <div class="btn-group">
                                        <a href="/backend/page/add/2" class="btn-default btn-xs btn">
                                            <i class="fa fa-plus"></i></a>
                                        <a href="/backend/page/delete/2" class="btn-xs btn-confirm btn-danger btn">
                                            <i class="fa fa-times fa-inverse"></i></a>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </li>
                        <li data-id="16">
                            <div class="tree-item">
                                <div class="title col-xs-7">
                                    <i class="fa fa-plus fa-fw item-expander"></i>
                                    <a href="/backend/page/edit/16"><i class="fa fa-file-o fa-fw"></i> User</a>
                                    <span class="label label-info">Редирект: user/profile</span>
                                    <a href="http://demo.kodicms.ru/user.html" class="item-preview" target="_blank">
                                        <span class="label label-info"><i class="fa fa-globe"></i> Просмотреть страницу</span>
                                    </a>
                                </div>
                                <div class="date col-xs-2 text-right text-muted">
                                    11.09.2014
                                </div>
                                <div class="status col-xs-2 text-right">
                                    <span class="label label-default editable-status editable editable-click" data-value="101">Скрыта</span>
                                </div>
                                <div class="actions col-xs-1 text-right">
                                    <div class="btn-group">
                                        <a href="/backend/page/add/16" class="btn-default btn-xs btn">
                                            <i class="fa fa-plus"></i></a>
                                        <a href="/backend/page/delete/16" class="btn-xs btn-confirm btn-danger btn">
                                            <i class="fa fa-times fa-inverse"></i></a>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </li>
                        <li data-id="17">
                            <div class="tree-item">
                                <div class="title col-xs-7">
                                    <a href="/backend/page/edit/17"><i class="fa fa-file-o fa-fw"></i> Protected page
                                    </a>
                                    <span class="label label-default">Страница ввода пароля</span>
                                    <a href="http://demo.kodicms.ru/protected-page.html" class="item-preview" target="_blank">
                                        <span class="label label-info"><i class="fa fa-globe"></i> Просмотреть страницу</span>
                                    </a>
                                </div>
                                <div class="date col-xs-2 text-right text-muted">
                                    11.09.2014
                                </div>
                                <div class="status col-xs-2 text-right">
                                    <span class="label label-default editable-status editable editable-click" data-value="101">Скрыта</span>
                                </div>
                                <div class="actions col-xs-1 text-right">
                                    <div class="btn-group">
                                        <a href="/backend/page/add/17" class="btn-default btn-xs btn">
                                            <i class="fa fa-plus"></i></a>
                                        <a href="/backend/page/delete/17" class="btn-xs btn-confirm btn-danger btn">
                                            <i class="fa fa-times fa-inverse"></i></a>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </li>
                    </ul>
                <? endif ?>
			</li>
		</ul>
		<ul id="page-search-list" class="tree-items no-padding-hr"></ul>
		<div class="clearfix"></div>
	</div>
</section>
<!-- /9. $UNIQUE_VISITORS_STAT_PANEL -->

