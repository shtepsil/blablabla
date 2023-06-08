$(function () {

    var interval = null;

    $('.q-arrow-minus,.q-arrow-plus').on("mousedown", function () {
        var $this = $(this);
        $this.attr('data-go', 'on');
        //        console.log('Клавиша нажата');

        interval = setInterval(function () {
            if ($this.attr('data-go') == 'on') {
                autoGo($this);
                clearInterval(this);
            } else clearInterval(this);
        }, 150);
    });

    $('.q-arrow-minus,.q-arrow-plus').on("mouseup", function () {
        var $this = $(this);
        clearInterval(interval);
        $this.attr('data-go', 'off');
    });

});

function cl(arg1, arg2) {
    if (arg2 === undefined) {
        console.log(arg1);
    } else {
        console.log(arg1, arg2);
    }
}

function isBoolean(val) {
    return val === false || val === true;
}

function autoGo(obj) {

    var $this = $(obj);
    if ($this.attr('data-type') == 'minus') {
        tcQuantityMinus($this.next());
    }
    if ($this.attr('data-type') == 'plus') {
        tcQuantityPlus($this.prev());
    }

}

// проверка на цифры
function isNumeric(input, type) {
    switch (type) {
        case 'n.':
            // цифры, точки
            inputV = input.value.replace(/[^\d\.]/g, '');
            input.value = roundToTwo(inputV, '.');
            break;
        case 'n,':
            // цифры, запятые
            inputV = input.value.replace(/[^\d\,]/g, '');
            input.value = roundToTwo(inputV, ',');
            break;
        default:
            // цифры
            var vall = input.value;
            //            console.log(vall);
            var n_val = vall.replace(/[^\d]/g, '');
            //            console.log(n_val);
            input.value = n_val;
    }
}

/**
 * После символа (запятая/точка)
 * оставляем только два знака
 * @param   {string}   str Обязательный аргумент
 * @param   {string} symbol Обязательный аргумент
 * @returns {string}
 */
function roundToTwo(str, symbol) {
    // по шаблону ищем в строке искомый символ
    var tpl = new RegExp(symbol, 'g');
    var result = str.match(tpl);

    // считаем количество символов symbol
    var counter = [];
    for (i in result) {
        if (counter[result[i]] != undefined) (counter[result[i]]++)
        else (counter[result[i]] = 1)
    }

    /**
     * Дополнительная проверка
     * на всякий случай проверим на null
     */
    if (result !== null) {
        // поверяем, есть ли в массиве искомый символ
        if (result.indexOf(symbol) != -1) {
            // Если искомых символов всего один
            if (counter[symbol] == 1) {
                // получаем номер позиции первого искомого символа
                var pos = str.indexOf(symbol);
                /**
                 * Проверку переменной pos делать не нужно
                 * потому что если искомых символов не будет, то
                 * этот внешний блок if(result !== null) не запустится
                 */
                // берем символы после первого искомого символа
                var decimal = str.substr(pos + 1, 2);
                // берем символы до первого искомого символа
                var example = str.split(symbol);
                // составляем строку {string}+{symbol}+{string}
                str = example[0] + symbol + decimal;
            } else {
                /*
                 * Если искомых символов больше одного
                 */
                // получаем номер позиции первого искомого символа
                var pos = str.indexOf(symbol);
                // берем символы до первого искомого символа
                var example = str.split(symbol);
                /**
                 * К символам взятым до первого искомого символа
                 * в конец подставляем искомый символ
                 * =============================================
                 * составляем строку {string}+{symbol}
                 */
                str = example[0] + symbol;
            }
        }
    }
    return str;
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
        toFixedFix = function (n, prec) {
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

/**
 * Response ajax
 * Обработка ajax запросов.
 * ! Только для debug
 */
function ajaxDebugDone(data, selector) {
    var res = $(selector);
    if (selector === undefined) {
        var res = $('.res');
    }
    res.html('Done<br><pre>' + prettyPrintJson.toHtml(data) + '</pre>');
}
function failDebugData(data, selector) {
    var res = $(selector);
    if (selector === undefined) {
        var res = $('.res');
    }
    res.html('Fail<br>' + JSON.stringify(data));
}