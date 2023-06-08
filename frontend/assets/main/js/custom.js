$(document).ready(function(){
    
    
    
    /**
     * Модальное окно со ссылками доставки
     * всплывашка tooltiper при наведении на вопросик
     */
    // При наведении на иконку вопросика
    $('.what-is-delivery').tooltipster({
        position : 'top',
        interactive: true,
        contentAsHTML: true,
        theme: ".tooltipster-default",
    });
    // При наведении на иконку грузовика
    $('.fa.fa-truck').tooltipster({
        position : 'top',
        interactive: true,
        contentAsHTML: true,
        theme: ".tooltipster-default",
    });
	
/* ==========================================================
                    HTML табы. Вкладки
 ============================================================ */
//	var tab = $('#tabs .tabs-items > div'); 
//	tab.hide().filter(':first').show(); 
////	cl(window.location.hash);
//	// Клики по вкладкам.
//	$('#tabs .tabs-nav a').on('click', function(){
////		cl($(window.location.hash).offset().top);
////		return;
//		tab.hide();
//		tab.filter(this.hash).show();
//		$('#tabs .tabs-nav a').removeClass('active');
//		$(this).addClass('active');
////		window.scrollTo(0, $(window.location.hash).offset().top);
////		window.location.hash = this.hash;
//		return false;
//	}).filter(':first').click();
// 
//	// Клики по якорным ссылкам.
//	$('.tabs-target').on('click', function(){
//		$('#tabs .tabs-nav a[href=' + $(this).attr('href')+ ']').click();
//	});
//	
//	// Отрытие вкладки из хеша URL
//	if(window.location.hash){
////		$('#tabs-nav a[href=' + window.location.hash + ']').click();
//		$('.tabs-nav a[href=' + window.location.hash + ']').trigger('click');
//		window.scrollTo(0, $(window.location.hash).offset().top);
////		window.location.hash = this.hash;
//	}

	// End HTML табы. Вкладки
    
});//JQuery

$('body')
	.on('keyup change', '[data-change]', function (e) {  
		var obj = $(this);
		var change = $(this).data('change');
		var request = $(this).data();
		if (change == 'search') {

			if ($(obj).val().length < 2) { 
				$('.__wrapper__search__result').css('display', 'none');	
			}		 
			search_ajax(this,e)			
		}
	})

function search_show(bool) { 
    if (bool) {
        $('#f__header__search__input').addClass('focus');
        $('#wrapper__search__result').fadeIn(300);
    } else {
        $('#f__header__search__input').removeClass('focus');
        $('#wrapper__search__result').fadeOut(300);
        setTimeout(function () {
            //Скролл для поиска
            if ($('#wrapper__scroll__search').hasClass('mCustomScrollbar')) {			
				$('.__wrapper__search__result').css('display', 'none');				
            }
        }, 1000);
    }
}
var last_searh=true;
function search_ajax(obj,e) {  
    last_searh = false;

    if ($(obj).val().length > 2) {
        if (e.type=='keyup') {
            if ($('#f__header__search__input').hasClass('focus')) {

                search_show(false);
            }
            $.ajax({
                url: $(obj).closest('form').attr('action'),
                type: 'GET',
                dataType: 'JSON',
                data: {
                    query: $(obj).val()
                }, 
                success: function (data) { 
                    var block = ''; 					
					if (data.brands != '') { 
                        block = block + '<div class="wrapper__set__search__result">' + data.brands + '</div>';
                    }					
                    if (data.cats != '') {
                        block = block + '<div class="wrapper__set__search__result">' + data.cats + '</div>';
                    }
                    if (data.items != '') {
                        block = block + '<div class="wrapper__search__result">' + data.items + '</div>';
                    }
                    if (block != '') {	
						last_searh = true;
						$('.search_count').text(data.count);
						$('.wrapper__scroll__search').html($.parseHTML(block));
						$('.__wrapper__search__result').css('display', 'block');						
						search_show(true);				
                    }else{
                        last_searh =false
                    }
                },
                error: function () {
                }
            });
        }
    }else{
        search_show(false);
    }
}