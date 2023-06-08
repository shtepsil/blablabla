function goods_inner_slide() {
    $('.goodsPosition .gImage .image_mini').on('click', 'li', function(){
        if ($(this).data('type') == "image" && !$(this).hasClass('current')) {
//            $('.goodsPosition .gImage .image').html('');
            $('.goodsPosition .gImage .image img').attr('src',$(this).attr('data-preview'));
            $('.goodsPosition .gImage .image img').attr('srcset',$(this).attr('data-srcset'));
//            $('.goodsPosition .gImage .image').css('background-image', $(this).css('background-image'));
            $('.goodsPosition .gImage .image_mini li').removeClass('current');
            $(this).addClass('current');
        } else if ($(this).data('type') == "video" && !$(this).hasClass('current')) {
            $('.goodsPosition .gImage .image').html($(this).html());
            $('.goodsPosition .gImage .image_mini li').removeClass('current');
            $(this).addClass('current');
        }
    });
}