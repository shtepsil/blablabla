<?php

use common\components\Debugger as d;

?>
<div class="tl-wrap">
    <div class="time-cooking-block">
        <div class="hours-block">
            <button type="button" class="q-arrow-minus" data-type="minus" data-go="off" onclick="tcQuantityMinus(this)"> - </button>
            <input
                class="form-control q-num g5" type="text" max="99" value="<?=$hours?>"
                data-type="hours"
                onkeyup="isNumeric(this);tcProcessingNumber(this);"
                onblur="tcInputBlur(this)"
                onfocus="tcFocus(this);"
            />
            <button type="button" class="q-arrow-plus" data-type="plus" data-go="off" onclick="tcQuantityPlus(this)"> + </button>
            <div class="time-name">Часы</div>
        </div>
        <div class="minutes-block">
            <button type="button" class="q-arrow-minus" data-type="minus" data-go="off" onclick="tcQuantityMinus(this)"> - </button>
            <input
                class="form-control q-num" type="text" max="60" value="<?=$minutes?>"
                data-type="minutes"
                onkeyup="isNumeric(this);tcProcessingNumber(this);"
                onblur="tcInputBlur(this)",
                onfocus="tcFocus(this);"
            />
            <button type="button" class="q-arrow-plus" data-type="plus" data-go="off" onclick="tcQuantityPlus(this)"> + </button>
            <div class="time-name">Минуты</div>
        </div>
        <div class="small-desc-block">
            <input
                class="form-control small-desc" type="text"
                placeholder="Краткое описание приготовления" value="<?=$small_desc?>"
                onkeyup="tcCopyContent(this);"
            />
        </div>
        {input}
        <div class="border-right"></div>
    </div>
</div>
<script>
function tcQuantityMinus(obj) {
    var $this = $(obj),
        parent = $this.parent(),
        input = parent.find('input'),
        val = +(input.val());
    if (val > 1) {
        val--;
        if(val < 10) val = '0'+val;
        input.val(val);
    }else{
        input.val('00')
    }

    tcInputTimeCooking(input);

}

function tcQuantityPlus(obj) {
    var $this = $(obj),
        parent = $this.parent(),
        input = parent.find('input'),
        val = +(input.val()),
        max = +(input.attr('max'));
    val++;
    if(val > max) val = max;
    if(val < 10) val = '0'+val;
    input.val(val);

    tcInputTimeCooking(input);

}
function tcProcessingNumber(obj){
    var $this = $(obj),
        max = +($this.attr('max')),
        val = +($this.val());

    if(val > max) $this.val(max);
    if($this.val() == '0') $this.val('');
    if($this.val() == '00') $this.val(max);
    if($this.val() == '000') $this.val('00');

    tcInputTimeCooking($this);

}
function tcInputBlur(obj){
    var $this = $(obj),
        // Обрезаем все пробелы, все пробелы заменяем на пустоту.
        val = $this.val().trim().replace(' ','').replace('0','').replace('00','');

//    $this.select(false);

    if(val == '' && val != '00') $this.val('00');
    if(val != ''){
        if(+val < 10) $this.val('0'+val);
    }
}

function tcFocus(obj){
    var $this = $(obj);
    $this.select();
}

function tcCopyContent(obj){
    var $this = $(obj),
        wrap = $('.field-recipes-description_time_cooking'),
        textarea = wrap.find('.description-time-cooking');
    textarea.val($this.val());
}

function tcInputTimeCooking($this){

//    console.log($this.val());

    var wrap = $('.time-cooking-block'),
        hours = wrap.find('[data-type=hours]'),
        minutes = wrap.find('[data-type=minutes]'),
        time_cooking = wrap.find('.quantity-num');
    time_cooking.val(hours.val()+':'+minutes.val());
//    console.log(hours.val()+':'+minutes.val());
}

</script>