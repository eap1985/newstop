// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';
require('bootstrap/dist/js/bootstrap.bundle');

var $ = require('jquery');
import '../scss/newstop.css';

$.NewsTop = function(options){

    var defaults = {
        tr : true,
        p : 1,
        tl : 0,
        tls : 0,
        speedAjax : 2000,
        speedNewLine : 2000,
        speedFade : 2000,
        speedHeight : 1000,
        uniqueId : 0,
        arr : '',
        first : true
    }

    var optionstwit = $.extend(defaults, options);

    optionstwit.myIntervaltwit = setInterval(checktop,optionstwit.speedAjax);

    /* Основная функция бегущей строки */
    function checktop()
    {
        clearInterval(optionstwit.myIntervaltwit)

        $.ajax({
            url:optionstwit.urlAjax,
            type:'GET',
            cache: true,
            data: { p: optionstwit.p },
            success: function(data){

                optionstwit.arr = '<div>';

                $.each(data.items, function(i,item){

                    optionstwit.uniqueId = ++optionstwit.uniqueId

                    optionstwit.arr +='<div id="tweet-id-'+optionstwit.uniqueId+'" class="twtr-tweet" style="opacity:0;height:0px;">';

                    optionstwit.arr +='<div class="twtr-tweet-wrap">';

                    optionstwit.arr +='<div class="twtr-avatar">';

                    optionstwit.arr +='<div class="twtr-img">';
                    optionstwit.arr +='<a href="'+item.h+'" rel="'+item.o+'" target="_blank"><img src="'  + item.src  +'"></a></div>';

                    optionstwit.arr +='</div>';

                    optionstwit.arr +='<div class="twtr-tweet-text">';

                    optionstwit.arr +='<p><a class="twtr-user" href="'+item.h+'" target="_blank">Александр Ермаков</a> '+ item.n;
                    optionstwit.arr +='</p>';
                    optionstwit.arr +='<em><a href="'+item.h+'" class="twtr-timestamp" target="_blank">'+item.t+'</a> <a class="twtr-user" href="'+item.h+'" target="_blank">читать далее...</a></em>';

                    optionstwit.arr +='</div>';

                    optionstwit.arr +='</div>';

                    optionstwit.arr +='</div>';

                });
                optionstwit.arr += '</div>';

                $.each(data.count, function(i,item){
                    optionstwit.p = parseInt(item.curr);
                    optionstwit.m = Math.ceil(item.max);
                });

                if (optionstwit.p == 0) optionstwit.p = optionstwit.p + 2
                else optionstwit.p = ++optionstwit.p


                optionstwit.tl = $(optionstwit.arr).find('div.twtr-tweet').length
                if(optionstwit.tl > 0)
                {
                    optionstwit.myIntervalnewline = setInterval(newline,optionstwit.speedNewLine);

                }
            },
            dataType:'json'
        });

    }

    /* Функция добавления строк и удаления устаревших */
    function newline(){
        clearInterval(optionstwit.myIntervalnewline)

        optionstwit.tls = ++ optionstwit.tls

        if (optionstwit.first) {
            optionstwit.tls = 4
            optionstwit.first = false
        }

        if (optionstwit.tr)
        {
            var itemnew = $(optionstwit.arr).find('div.twtr-tweet:eq('+(optionstwit.tls-1)+')');

            var itemnewId = $(itemnew).attr('id');

            $('.twtr-reference-tweet').after(itemnew);

            $('#'+itemnewId).animate({'height':'90'},optionstwit.speedHeight,function(){

                $(this).fadeTo(optionstwit.speedFade,1)

                clearInterval(optionstwit.myIntervalnewline)
                optionstwit.myIntervalnewline = setInterval(newline,optionstwit.speedNewLine);


                if(optionstwit.tls == optionstwit.tl)
                {
                    optionstwit.tls = 0

                    optionstwit.myIntervaltwit = setInterval(checktop,optionstwit.speedAjax);
                    clearInterval(optionstwit.myIntervalnewline)
                }

            });

            if($('div.twtr-tweet').length > 5)
            {
                $('div.twtr-tweet').slice(5,$('div.twtr-tweet').length).remove();
            }
        }


    }
    /* Обработчик приостанавливающая вывод строк */
    $('.twtr-timeline').mouseover(function(){
        optionstwit.tr = false;
        clearInterval(optionstwit.myIntervalnewline)
    });

    /* Обработчик возобновляющий вывод строк */
    $('.twtr-timeline').mouseleave(function(){
        optionstwit.tr = true;
        clearInterval(optionstwit.myIntervalnewline)
        optionstwit.myIntervalnewline = setInterval(newline,optionstwit.speedNewLine);

    });

}

$(document).ready(function() {



    console.log(123);

    $('#event_opisanie').each(function() {
        let $textarea =$(this);
        var offset = this.offsetHeight - this.clientHeight;
        var resizeTextarea = function() {
            $textarea.css('height', 'auto')
                .css('height', $textarea.prop('scrollHeight') + offset);
        };
        $textarea.on('keyup input', resizeTextarea);
        resizeTextarea();
    });
    // наш плагин TwitterTop
    $.NewsTop({
        urlAjax: '/newsajax/', // страница запроса
        speedAjax : 2000, // скорость запроса
        speedNewLine : 4000, // скорость появления строки
        speedFade : 2000, // скорость увеличения прозрачности
        speedHeight : 2000 // скорость выдвижения новой строки


    });
});




