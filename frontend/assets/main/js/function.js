var media_type = null;
var body_scroll = $('html, body');

if ($(window).width() > 999) {
    media_type = 'desktop';
} else if ($(window).width() < 1000 && $(window).width() > 767) {
    media_type = 'tablet';
} else if ($(window).width() < 768) {
    media_type = 'mobile';
}

$(window).resize(function() {
   if ($(this).width() > 999) {
       media_type = 'desktop';
   } else if ($(this).width() < 1000 && $(this).width() > 767) {
       media_type = 'tablet';
   } else if ($(this).width() < 768) {
       media_type = 'mobile';
   }
});

function cl(arg1, arg2) {
    if (arg2 === undefined) {
        console.log(arg1);
    } else {
        console.log(arg1, arg2);
    }
}


//Рассчёт высоты футера
function setFooter(status) {
    status = status || false;
    if (status == 'reset') {
        $('#global').css('padding-bottom', '');
        $('.footer').css('margin-top', '');
    }
    $('#global').css('padding-bottom', $('.footer').outerHeight(true));
    $('.footer').css('margin-top', -$('.footer').outerHeight(true));
}

//Горизонтальное меню
function topMenu() {
    $('.topMenu').off('click', '.dropmenu');
    $('.topMenu').on('click', '.dropmenu', function(){
       if (!$(this).hasClass('open')) {
           $('.topMenu .dropmenu').removeClass('open');
           $(this).addClass('open');

           if ($(window).width() < 1000) {
               $(document).scrollTop($(this).offset().top);
           }

           var doc_click = function (e) {
               if ($(e.target).closest('.topMenu').length == 0) {
                   $('.topMenu .dropmenu').removeClass('open');
                   $(document).off('click', doc_click);
               }
           };
           $(document).off('click', doc_click);
           $(document).on('click', doc_click);
       } else {
           $(this).removeClass('open');
       }
    });
}

//Приведение к общей высоте
function check_height() {
    var all_height = 0;
    if ($(window).width() > 999) {
        $('[data-check="height"]').children().css('height', 'auto');
        setTimeout(function(){
            $('[data-check="height"]').children().each(function(){
                if ($(this).outerHeight() > all_height) {
                    all_height = $(this).outerHeight();
                }
            });
            $('[data-check="height"]').children().css('height', all_height);
        }, 600);
    } else {
        $('[data-check="height"]').children().css('height', 'auto');
    }

}

//Табы/вкладки

function build_tabs() {
    $('[data-type="tabs"] [data-type="thead"]').on('click', 'li', function(e){
        if (!$(this).hasClass('current')) {
            $(this).parent().children('li').removeClass('current').eq($(this).index()).addClass('current');
            //$(this).parent().children('li').eq($(this).index()).addClass('current');
            $(this).closest('[data-type="tabs"]').find('[data-type="tbody"]').children('li').removeClass('current').eq($(this).index()).addClass('current');
        } else {
            if ($(e.target).closest($(this).children('.tBody')).length == 0) {
                $(this).removeClass('current');
            }
        }
    });
}

function cabinetMenu() {
    $('.wrapperOptions .topEnter_icon, .wrapperOptions .topEnter').on('click', function(){
        if (!$('.wrapperOptions ul.cabinetSubmenu').hasClass('open')) {
            $(this).addClass('open');
            $('.wrapperOptions ul.cabinetSubmenu').addClass('open');
            var doc_click = function (e) {
                if ($(e.target).closest('.wrapperOptions').length == 0) {
                    $('.wrapperOptions .cabinetSubmenu').removeClass('open');
                    $(document).off('click', doc_click);
                }
            };
            $(document).off('click', doc_click);
            $(document).on('click', doc_click);
        } else {
            $(this).removeClass('open');
            $('.wrapperOptions ul.cabinetSubmenu').removeClass('open');
        }
    });
}

function listen_tab() {
    $('[data-tab="head"]').children('li').removeClass('current').eq(0).addClass('current');
    $('[data-tab="body"]').children('li').removeClass('current').eq(0).addClass('current');
    $('[data-tab="head"]').on('click', 'li', function(){
       if (!$(this).hasClass('current')) {
           $(this).parent().children('li').removeClass('current').eq($(this).index()).addClass('current');
           $(this).parent().next().children('li').removeClass('current').eq($(this).index()).addClass('current');
       }
    });
}

function listen_cart() {
    $('.topCart').on('click', '.wrapperClick', function(){
       if (!$(this).parent().hasClass('open')) {

           $('.wrapperOptions .topEnter').removeClass('open');
           $('.wrapperOptions .cabinetSubmenu').removeClass('open');

           $(this).parent().addClass('open');

           //$('#modalCartWindow').css('height', $(window).height() - $('#modalCartWindow').offset().top - 10)

           $('#cart_items').css('height', $(window).height() - $('#modalCartWindow').offset().top - $('#modalCartWindow .topTitle').outerHeight() - $('#modalCartWindow .bottomTitle').outerHeight() - 10 );

           $(window).resize(function(){
               $('#cart_items').css('height', $(window).height() - $('#modalCartWindow').offset().top - $('#modalCartWindow .topTitle').outerHeight() - $('#modalCartWindow .bottomTitle').outerHeight() - 10 );
           });

           $('.wrapperOverlay').fadeIn('slow');
           //var doc_click = function (e) {
           //    if ($(e.target).closest('.topCart').length == 0) {
           //        $('.topCart').removeClass('open');
           //        $('.wrapperOverlay').fadeOut('slow');
           //        $(document).off('click', doc_click);
           //    }
           //};
           //$(document).off('click', doc_click);
           //$(document).on('click', doc_click);
           $('.wrapperOverlay').on('click', function(){
               $('.topCart').removeClass('open');
               $('.wrapperOverlay').fadeOut('slow');
           });
       } else {
           $(this).parent().removeClass('open');
           $('.wrapperOverlay').fadeOut('slow');
       }
    });
}

function list_menu() {
    var header_height = 0;
    header_height = $('header.header').outerHeight();
    $('.navMenu').css('top', header_height);
    $(window).resize(function(){
        header_height = $('header.header').outerHeight();
        $('.navMenu').css('top', header_height);
    });
    $('.iconMenu').on('click', function(){
        header_height = $('header.header').outerHeight();
        $('.navMenu').css('top', header_height);
        if (!$('.navMenu').hasClass('open')) {
            $('.navMenu').addClass('open');
            check_height_content(true);
            var doc_click = function (e) {
                if ($(e.target).closest('.navMenu').length == 0 && $(e.target).closest('.iconMenu').length == 0) {
                    $('.navMenu').removeClass('open');
                    check_height_content(false);
                    $(document).off('click', doc_click);
                }
            };
            $(document).off('click', doc_click);
            $(document).on('click', doc_click);
        } else {
            check_height_content(false);
            $('.navMenu').removeClass('open');
        }
    });
    function check_height_content(bool) {
        //if (bool) {
        //    var all_height = $('header.header').outerHeight() + $('.navMenu').outerHeight();
        //    $('#global').css('height', all_height).css('overflow', 'hidden');
        //} else {
        //    $('#global').css('height', '').css('overflow', 'visible');
        //}
    }
}

function listen_li_spoiler() {
    $('[data-type="spoilerhead"]').on('click', function(){
       $(this).toggleClass('open', '');
        if ($('[data-type="spoilerhead"]').hasClass('open')) {
            $(this).parent().addClass('opening');
        } else {
            $(this).parent().removeClass('opening');
        }
    });
}

function listen_manager_acc() {
    $('.accountManager .description').on('click', '.nameManager', function(){
       if (!$('.accountManager').hasClass('open')) {
           $('.accountManager').addClass('open');
           var doc_click = function (e) {
               if ($(e.target).closest('.accountManager').length == 0) {
                   $('.accountManager').removeClass('open');
                   $(document).off('click', doc_click);
               }
           };
           $(document).off('click', doc_click);
           $(document).on('click', doc_click);
       } else {
           $('.accountManager').removeClass('open');
       }
    });
}

function listen_manager_acc_change() {
    $('.managerSelect .description').on('click', '.nameManager', function(){
        if (!$('.managerSelect').hasClass('open')) {
            $('.managerSelect').addClass('open');
            var doc_click = function (e) {
                if ($(e.target).closest('.managerSelect').length == 0) {
                    $('.managerSelect').removeClass('open');
                    $(document).off('click', doc_click);
                }
            };
            $(document).off('click', doc_click);
            $(document).on('click', doc_click);
        } else {
            $('.managerSelect').removeClass('open');
        }
    });
}

function open_mobile_acc_manager() {
    $('.iconAccountManager').on('click', function(){
        if (!$(this).hasClass('open')) {
            $(this).addClass('open');
            $('.accountManager').fadeIn('slow');
        } else {
            $(this).removeClass('open');
            $('.accountManager').fadeOut('slow');
        }
    });
}

function open_mobile_menu_manager() {
    $('.iconMenu_manager').on('click', function(){
        if (!$(this).hasClass('open')) {
            $(this).addClass('open');
            $('.menuManager').fadeIn('slow');
        } else {
            $(this).removeClass('open');
            $('.menuManager').fadeOut('slow');
        }
    });
}



function cart_fixed_block() {
    var fix_block_width = 0,    //ширина скролл-блока
        cartList_height = 0,    //высота контента для скролл-блока
        wrap_fixed = 0,         //высота скролл-блока
        top_control = 0,        //значение старта
        bottom_control = 0;     //значение финиша

    $(window).resize(function() {
       cart_param();
    });

    $(document).on('scroll', function(){
       if ($(this).scrollTop() >= top_control && $('#cart_list').outerHeight() > $('#cart_right').outerHeight() && $(window).width() > 999) {
           fixed_bugs();
           $('#wrap_fixed').css('width', fix_block_width);
           $('#wrap_fixed').addClass('fixed');
           $('#cart_right').css('height', wrap_fixed);
           if ($(this).scrollTop() >= bottom_control && $('#cart_list').outerHeight() > $('#cart_right').outerHeight()) {
               $('#wrap_fixed').removeClass('fixed');
               $('#wrap_fixed').addClass('absolute');
           } else {
               $('#wrap_fixed').removeClass('absolute');
               $('#wrap_fixed').addClass('fixed');
           }
       } else {
           $('#wrap_fixed').css('width', '');
           $('#wrap_fixed').removeClass('fixed');
           $('#cart_right').css('height', '');
       }
    });

    cart_param();

    if ($(document).scrollTop() >= top_control && $('#cart_list').outerHeight() > $('#cart_right').outerHeight() && $(window).width() > 999) {
        fixed_bugs();
        $('#wrap_fixed').css('width', fix_block_width);
        $('#wrap_fixed').addClass('fixed');
        $('#cart_right').css('height', wrap_fixed);
        if ($(document).scrollTop() >= bottom_control && $('#cart_list').outerHeight() > $('#cart_right').outerHeight()) {
            $('#wrap_fixed').removeClass('fixed');
            $('#wrap_fixed').addClass('absolute');
        } else {
            $('#wrap_fixed').removeClass('absolute');
            $('#wrap_fixed').addClass('fixed');
        }
    } else {
        $('#wrap_fixed').css('width', '');
        $('#wrap_fixed').removeClass('fixed');
        $('#cart_right').css('height', '');
    }


    function cart_param() {
        fix_block_width = $('#cart_right').outerWidth();
        cartList_height = $('#cart_list').outerHeight();
        wrap_fixed = $('#wrap_fixed').outerHeight();
        top_control = $('#cart_list').offset().top;
        bottom_control = top_control + cartList_height - wrap_fixed;
    }

    function fixed_bugs() {
        cartList_height = $('#cart_list').outerHeight();
        wrap_fixed = $('#wrap_fixed').outerHeight();
        bottom_control = top_control + cartList_height - wrap_fixed;
    }
}

function goto_reviews() {
    $('[data-goto="reviews"]').on('click', function() {
        console.log(media_type);
        if (media_type == 'desktop') {
            $('li.scReviews').trigger('click');
            $(body_scroll).animate({scrollTop: $('.scReviews').offset().top}, 500);
        } else if (media_type == 'tablet' || media_type == 'mobile') {
            $('li.scReviews_mob').trigger('click');
            $(body_scroll).animate({scrollTop: $('.scReviews_mob').offset().top}, 500);
        }
    });
}


function subsub_listen() {

    if ($(window).width() < 1000) {
        $('ul.topMenu li ul.submenu [data-subsub=true]>a').on('click', function(e) {
            e.preventDefault();
            $('.topMenu').off('click', '.dropmenu');

            if (!$(this).parent().children('ul.subsub').hasClass('open')) {
                $(this).parent().children('ul.subsub').addClass('open');
                $(this).css('display', 'none');
                $(this).parent().children('ul.subsub').css('display', 'block');
            }

            $(this).parent().children('ul.subsub').children('li').eq(0).off('click');
            $(this).parent().children('ul.subsub').children('li').eq(0).on('click', function() {

                this_this = $(this);

                this_this.parent().parent().children('a').css('display', 'block');
                this_this.parent().parent().children('ul.subsub').css('display', 'none');

                setTimeout(function(){
                    this_this.parent().parent().children('ul.subsub').removeClass('open');
                    topMenu();
                }, 500);
            });

        });
    } else {
        $('ul.topMenu li ul.submenu').off('click', '[data-subsub=true]');
        topMenu();
    }
}

/***
 number - исходное число
 decimals - количество знаков после разделителя
 dec_point - символ разделителя сотых
 thousands_sep - разделитель тысячных
 синтаксис - number_format(totalSumm, 2, ',', ' ')
 ***/
function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec);
            return '' + (Math.round(n * k) / k)
                .toFixed(prec);
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
        .split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '')
        .length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1)
            .join('0');
    }
    return s.join(dec);
}



// function subsub_listen() {
//
//     if ($(window).width() < 1000) {
//         $('ul.topMenu li ul.submenu').on('click', '[data-subsub=true]', function(e) {
//             e.preventDefault();
//             $('.topMenu').off('click', '.dropmenu');
//
//             if (!$(this).children('ul.subsub').hasClass('open')) {
//                 $(this).children('ul.subsub').addClass('open');
//                 $(this).children('a').css('display', 'none');
//                 $(this).children('ul.subsub').css('display', 'block');
//             }
//
//             $(this).children('ul.subsub').children('li').eq(0).off('click');
//             $(this).children('ul.subsub').children('li').eq(0).on('click', function() {
//
//                 this_this = $(this);
//
//                 this_this.parent().parent().children('a').css('display', 'block');
//                 this_this.parent().parent().children('ul.subsub').css('display', 'none');
//
//                 setTimeout(function(){
//                     this_this.parent().parent().children('ul.subsub').removeClass('open');
//                     topMenu();
//                 }, 500);
//             });
//
//         });
//     } else {
//         $('ul.topMenu li ul.submenu').off('click', '[data-subsub=true]');
//         topMenu();
//     }
// }