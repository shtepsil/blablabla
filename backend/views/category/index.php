<?php
/**
 * @var common\models\Category[] $cats
 * @var $this yii\web\View
 */
use backend\assets\CatalogAsset;
use common\models\Category;
use yii\helpers\Json;
use yii\helpers\Url;

CatalogAsset::register($this);
?>

<?= $this->render('//blocks/breadcrumb') ?>
<section id="content">
    <div class="page-mail">
        <div class="mail-nav">
            <div class="navigation">
                <div class="compose-btn">
                    <div class="btn-group">
                        <a href="<?= Url::to(['category/control']) ?>" class="btn-primary btn"><i class="fa fa-plus"></i>
                            <span class="hidden-xs hidden-sm">Создать категорию</span></a>
                    </div>
                </div>
                <div class="sections-list">
                    <ul class="nav nav-pills nav-stacked category-list">
						<?php if ($cats) :?>
							<?php foreach ($cats as $cat): ?>
								<?php
								/**
								 * @var Category $main
								 * @var Category[] $children
								 */
								$class = 'fa-table';
								$main = $cat['main'];
								$children = $cat['children'];
								$url_add_item = ['items/control', 'cat' => $main->id];
								if ($main->type == 'cats') {
									$class = 'fa-folder-o';
									$url_add_item = ['category/control', 'parent' => $main->id];
								}
								?>
								<li>
									<div class="category">
										<div class="actions text-right">
											<div class="btn-group">
												<a class="btn-default btn-xs" href="<?= Url::to($url_add_item) ?>">
													<i class="fa fa-plus"></i>
												</a>
												<a class="btn-default btn-xs" href="<?= Url::to(['category/control', 'id' => $main->id]) ?>">
													<i class="fa fa-edit"></i>
												</a>
												<a class="btn-xs btn-confirm btn-danger" href="<?= Url::to(['category/deleted', 'id' => $main->id]) ?>">
													<i class="fa fa-times fa-inverse"></i></a>
											</div>
										</div>
										<a href="#" class="sub-lists" data-type="<?= $main->type ?>" data-status="close" data-id="<?= $main->id ?>"><i class="fa  <?= $class ?>"></i> <?= $main->name ?></a>
									</div>
									<?php if ($children): ?>
										<?= $this->render('sub_cats', array('cats' => $children, 'item' => $main)) ?>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						 <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        <? $this->registerCss(<<<CSS
.headline-actions>.btn-toolbar+.btn-toolbar {
    margin-top: 5px;
}
CSS
)?>
        <div class="mail-container panel">
            <div class="mail-controls clearfix headline-actions">
                <div class="btn-toolbar" role="toolbar">
                    <div class="btn-group">
                        <a href="<?= Url::to(['items/control']) ?>" class="btn-primary btn" data-hotkeys="ctrl+a"><i class="fa fa-plus"></i>
                            <span class="hidden-xs hidden-sm">Создать товар</span></a>
                    </div>
                    <div class="btn-group">
                        <a href="<?= Url::to(['type-handling/index']) ?>" class="btn-primary btn"><i class="fa fa-scissors"></i>
                            <span class="hidden-xs hidden-sm">Обработка</span></a>
                    </div>
                    <div class="btn-group">
                        <a href="<?= Url::to(['category/transport']) ?>" class="btn-primary btn"><i class="fa fa-exchange"></i>
                            <span class="hidden-xs hidden-sm">Перемещение</span></a>
                    </div>
                    <? if (false): ?>
                        <div class="btn-group fields-control pull-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    Отображаемые поля <i class="fa fa-caret-down"></i>
                                </button>
                                <ul class="dropdown-menu padding-sm" role="menu">
                                    <li>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="in_headline" value="11">
                                            Answer </label>
                                    </li>
                                    <li>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="in_headline" value="20">
                                            Category </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    <? endif ?>
                </div>
                <div class="btn-toolbar" role="toolbar">
                    <div class="btn-group">
                        <a href="<?= Url::to(['items/export']) ?>" class="btn-primary btn"><i class="fa fa-upload"></i>
                            <span class="hidden-xs hidden-sm">Экспорт</span></a>
                    </div>
                    <div class="btn-group">
                        <a href="<?= Url::to(['items/import']) ?>" class="btn-primary btn"><i class="fa fa-download"></i>
                            <span class="hidden-xs hidden-sm">Импорт</span></a>
                    </div>
					<div class="btn-group">
						<a href="<?= Url::to(['items/yml']) ?>" class="btn-primary btn"><i class="fa fa-file-text"></i>
							<span class="hidden-xs hidden-sm">Yml</span></a>
					</div>
                </div>

            </div>
            <div class="mail-controls">
                <div id="toolbar">
                    <div class="form-search">
                        <div class="input-group" id="search_form">
                            <div class="input-group-btn">
                                <select name="search_field" class="form-control" style="width: 150px" tabindex="-1" title="">
                                    <option value="name" selected="selected">Название</option>
                                    <option value="article" >Артикул</option>
                                    <option value="id">ID</option>
                                </select></div>
                            <input type="text" name="name" id="input_search_form" class="form-control search-input" value="" placeholder="Поиск">
                            <div class="input-group-btn">
                                <button class="btn btn-default" id="send_search_form" type="button"><i class="fa fa-search"></i> Поиск</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mail-list headline" id="items">
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</section>
<?php
$url_filter = Url::to(['category/filter']);
$url_edit_attr = Json::encode(Url::to(['items/edit-attr']));
$loader_html = '<div class="loader_cms"><img src="' . Url::base() . '/images/loading.gif" alt=""></div>';
$this->registerJs(<<<JS
$('select[name="search_field"]').on('change', function () {
    $('.search-input').prop('name', $(this).val());
});
$('#search_form').on('click', '#send_search_form', function (e) {
    e.preventDefault();
    var input = $('#input_search_form', '#search_form');
    var field = $(input).attr('name');
    var search = {};
    search[field] = $(input).val();
    filter['search'] = search;
    composeFilter();
});
var filter = parseParms(window.location.hash);
var order = {};
var limit;
var offset;
var itemcount = 0;
var checkeds = [];
if (window.location.hash) {
    if (filter) {
        var reg = /(search)\[(.*)\]/i;
        $.each(filter, function (index, val) {
            var match = index.match(reg);
            if (match) {
                var input = $('#input_search_form', '#search_form');
                var field = $(input).prop('name', match[2]);
                $('select[name="search_field"]').val(match[2])
                $(input).val(val);
            }
        })
    }
    if (filter['cat']) {
        var click_a = $('.sub-lists[data-id=' + filter['cat'] + ']');
        click_a.addClass('active');

        click_a.parents('.sub-list').each(function (key, el) {
            var obj = $(el).prev('.category').find('.sub-lists');
            var id = $(obj).data('id');
            if ($(obj).data('status') == 'close') {
                $('.fa-folder-o', obj).removeClass('fa-folder-o').addClass('fa-folder-open-o');
                $(obj).data('status', 'open');
                $('#sub-' + id).show();
            } else {
                $('.fa-folder-open-o', obj).addClass('fa-folder-o').removeClass('fa-folder-open-o');
                $('#sub-' + id).hide();
                $(obj).data('status', 'close');
            }
        })
    }
    //if (filter['order']) {
    //    delete filter['order'];
    //}
    //if (filter['search']) {
    //    $('.search > .input#search-input').val(filter['search']);
    //    $('.search > .input#search-input').keyup();
    //}
}
function parseParms(url) {
    var pos = url.indexOf('#');
    if (pos < 0) {
        return {};
    }
    var qs = url.substring(pos + 1).split('&');
    for (var i = 0, result = {}; i < qs.length; i++) {
        qs[i] = qs[i].split('=');
        result[decodeURIComponent(qs[i][0])] = decodeURIComponent(qs[i][1]);
    }
    return result;
}
$('#items').on('change', '.switcher_ajax', function (e) {
    instinct.update_attr(
        {$url_edit_attr},
        $(this).data('pk'),
        $(this).data('attr'),
        ($(this).prop('checked') ? $(this).data('enable') : $(this).data('disable'))
    )
}).on('click', 'th[data-sorting]', function (e) {
    e.preventDefault();
    filter['order'] = {};
    filter['order'][$(this).data('attr').toString()] = $(this).data('sorting');
    composeFilter();
});
function composeFilter() {
    var newHash = '',
        res = $('.res');
    newHash = $.param(filter);
    window.location.hash = newHash;
    $("#items").html('{$loader_html}');
    $.ajax({
        url: '{$url_filter}',
        data: {filter: newHash, _csrf: yii.getCsrfToken()},
        cache: true,
        type: 'POST',
        dataType: 'HTML',
        success: function (data) {
            $("#items").html(data);
            $('.switcher_ajax', '#items').switcher({
//				theme: 'square',
                on_state_content: '<span class="fa fa-check"></span>',
                off_state_content: '<span class="fa fa-times"></span>'
            });
            $('.editable_ajax', '#items').editable({
                mode: 'inline',
                emptytext: 'Пусто',
                validate: function (value) {
                    if ($(this).data('required') == 1) {
                        if ($.trim(value) == '') return 'Не может быть пустым';
                    }
                    if ($(this).data('rule') == 'numeric') {
                        if (/[^\d]/.test(value))
                            return 'Может быть только число';
                    }
                },
                url: function (params) {
                    instinct.update_attr(
                        {$url_edit_attr},
                        params.pk,
                        $(this).data('attr'),
                        params.value
                    )
                }
            });
        },
        error: function (data) {
            res.html(JSON.stringify(data));
        }
    });
}
composeFilter();

$('.sub-lists').on('click', function (e) {
    e.preventDefault();
    open_category(this);
});
$('.mail-container').on('click', '.pagination a', function (e) {
    e.preventDefault();
    filter['page'] = $(this).data('page');
    composeFilter();
});
function open_category(obj) {
    var id = $(obj).data('id');
    if ($(obj).data('type') == 'cats') {
        if ($(obj).data('status') == 'close') {
            $('.fa-folder-o', obj).removeClass('fa-folder-o').addClass('fa-folder-open-o');
            $(obj).data('status', 'open');
            $('#sub-' + id).show();
        } else {
            $('.fa-folder-open-o', obj).addClass('fa-folder-o').removeClass('fa-folder-open-o');
            $('#sub-' + id).hide();
            $(obj).data('status', 'close');
        }
    } else if ($(obj).data('type') == 'items') {
        if ($(obj).hasClass('active')){
            delete filter["cat"]
            $('.sub-lists').removeClass('active');
        }else{
            $('.sub-lists').removeClass('active');
            $(obj).addClass('active');
            filter["cat"] = id;
        }
        composeFilter();
    }
}
JS
)
?>
<script type="text/javascript">
</script>