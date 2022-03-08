// вспомогательные объекты
var slybox = {
color : "#fff"
}
// вспомогательные объекты
var slypreload = new Array();
var errorsrc = new Object();

$(function(){
	
	for(i = 0; i < $(".minislybox").length; i++) {
	slypreload[i] = false
	}
	// для быстрой генерации элементов
	$("body").append('<div id="slyload"><div id = "slyloader"></div><div class="unit-ratingdemo"></div><div class="topslydemo"></div><div id = "slyprev"></div><div id = "slynext"></div><div class="loadingdemo"></div></div>');

$("body").click(function(event) {
	if($("#slyconteiner").attr('terget') == '1'){
	
		if(event.target.id =='slyshadow'){
	
		delshadow()
	
		}
	}
});


/* внутри функции attevent - содержится  обработчик hendler события onload(load) для изображения
она принемает два параметра - newim - объект нового изображения и параметр e - объект содержащий
информацию об изображении первью (комментарий, src максимального изображения) */

function attevent(newim,e){
	// обработчик события onload(load)

	function hendler()
	{
	if(!errorsrc.error)	$('.sscontainer').append('<img id = "bigimg" src='+newim.src+'></img>');
	else {
		$('.sscontainer').append('<div id = "bigimg" class="errorbigimg">'+errorsrc.des+'</div>');
		$('.sscontainer').next().hide();
	
	}
	$("#slyconteiner").bind('mouseleave',mouseouthandler);
	$("#slyconteiner").bind('mouseenter',mouseoverhandler);
	// эффект затемнения - эффект сокрытия элементов страницы при показе увеличенного изображения
	// Получаем высоту всей старницы для установления свойства height элемента 
	// создающего эффект затемнения
	
	function getDocumentHeight() 
	{  
	return Math.max(document.compatMode != 'CSS1Compat' ? document.body.scrollHeight : document.documentElement.scrollHeight);  
	}  

	// Получаем ширину всей старницы для установления свойства width элемента 
	// создающего эффект затемнения
	function getDocumentWidth() {  
	return Math.max(document.compatMode != 'CSS1Compat' ? document.body.scrollWidth : document.documentElement.scrollWidth);  
	} 
	var iml = document.getElementById("slyconteiner").clientWidth;
	$("#slycommenttext").text($(".minislybox[active='active']").attr('alt')).css({'width':iml-20});
	var imt = document.getElementById("slyconteiner").clientHeight;
	
	bh = (imt > document.documentElement.clientHeight) ? getDocumentHeight() + 10 : getDocumentHeight();
	bw = (iml > document.documentElement.clientWidth) ? getDocumentWidth() + (iml - document.documentElement.clientWidth) : getDocumentWidth();


	
	$(document.body).append('<div id = "slyshadow" style="width:'+bw+'px;height:'+bh+'px;"></div>');
    
	// Прогрессбар - скрываем его так как изображения полностью загрузилось
	// Прогрессбар является элементом div, содержащим изображение, изменить его  можно
	// изменив параметр  background:url('ajax-loader.gif');  
	$('#slyload').hide();

	
	var imbt = document.documentElement.clientHeight;
	var offset = $(document).scrollTop();
	
	// центрирование изображения по высоте и по ширине body
	var centerimgh = (imt > document.documentElement.clientHeight) ?  10 : (imbt/2) - (imt/2);
	var centerimg = Math.abs((bw/2) - (iml/2)); 
	tlw = $('.tl').next().width();
	$('.tl').next().width(tlw-21);
	$('.bl').next().width(tlw-21);
	$('.bagie').width(iml-21);
	
	// текст комментария не должен выходить 
	
	//alert('('+$('#slyconteiner').height()+')-' + document.documentElement.clientHeight);
	// баг в ie 6 7
	$(".topsly,.botsly,.slymaincontainer,.rightsly").css({'width':iml});

	
	if($(".minislybox[active='active']").attr('alt')=='') $("#slycommenttext").css({'height':"10px"});

	// эфекты 
	$("#slyconteiner").hide().fadeOut('fast').css({'left':centerimg+'px','top':offset + centerimgh+'px'}).fadeIn('fast',function(){$("#slyconteiner").attr('terget','1');});
	
	var act = $(".minislybox").index($(".minislybox[active='active']"));
	slypreload[act] = true

	if(!slypreload[act+1]) {
		if ($(".minislybox").length > act + 1) {
			e = act+1;
			nextnewImnext = new Image();
			nextnewImnext.src = $(".minislybox:eq("+e+")").attr("src").replace(/_s/ig,"");
			slypreload[e] = true;
			
			}
	}		
	if (!slypreload[act-1]) {	
	e = act-1;
			if(e >= 0) {
			nextnewImnext = new Image();
			nextnewImnext.src = $(".minislybox:eq("+e+")").attr("src").replace(/_s/ig,"");
			}
			slypreload[e] = true;
			}

		
	
	}

	function hendlere(){
	errorsrc = {'error':true,'des':'Ошибка: отсутствует увеличенное изображение!'}
	hendler()
	}
	errorsrc = {'error':false}
	// кроссбраузерный обработчик 
	$(newim).load(hendler);
	$(newim).error(hendlere);
}

// функция bigimg основная функция для создания нового изображения и удаления предыдущего 
// функция принемает один параметр (e) - содержащий src изображения
function bigimg(e)
{

	$("#slynext,#slyprev,#slycomment,#slyconteiner,.slymaincontainer,.sscontainer").remove();

	
	$('body').append('<div id = "slyconteiner"><div class="topsly"><div class="tlsly"></div><div class="bagie" ></div><div class="trsly"></div></div><div class="slymaincontainer"><div class="sscontainer" ></div><div id = "slycomment" style="background:'+slybox.color+'"><div id = "slyclose"></div><div id = "slycommenttext" style="background:'+slybox.color+'"></div></div></div><div class="botsly"><div class="blsly"></div><div class="bagie"></div><div class="brsly"></div></div><div id = "slyprev"></div><div id = "slynext"></div></div>');
	$(".slymaincontainer,.bagie").css({"background-color":slybox.color});
	
	if (/MSIE (5\.5|6).+Win/.test(navigator.userAgent)) {
	
	$($("#slyclose")).fixPNG($("#slyclose"));
	$($("#slyload")).fixPNG($("#slyload"));
	}
	//функци с элементом canvas для создания уголков
	function createCorner(b) {

    var a = $('.'+b);
	a.width("12px");
	a.height("12px");
	var d = 12,
	e = 12;

	position = {
	top: b.charAt(0) == "t",
	left: b.charAt(1) == "l"
	};
	
		
	var canvas = document.createElement('canvas');
    $(a).append(canvas);
	var G_vmlCanvasManager;

	// code for IE browsers
	if (window.G_vmlCanvasManager)
	{
	canvas = window.G_vmlCanvasManager.initElement(canvas);

	}
	var context = canvas.getContext('2d');

	canvas.width = 12;
	canvas.height = 12;
	$(canvas).css({"float":"left"});
	
	
	a = context;
	
	a.fillStyle = slybox.color;
	a.arc(position.left ? d : e - d, position.top ? d : e - d, d, 0, Math.PI * 2, true);
	a.fill();
	a.fillRect(position.left ? d : 0, 0, e - d, e);
	a.fillRect(0, position.top ? d : 0, e, e - d)
	}

	$(".tlsly,.trsly,.blsly,.brsly").each(function(){
	$(this).css({"background-color":"transparent"});
		createCorner($(this).attr("class"))
	});
	
	// сокрытие элементов управления 
	$("#slynext,#slyprev").hide();

	// получение количества коментариев, находящихся внутри элемента div с целью их удаления

	//удаление максимального изображения вместе с тенью
	if(document.getElementById("slyshadow")){
	$('#slyshadow,#slyconteiner #bigimg').remove();
	//удаление максимального изображения вместе с тенью
	}
	// создание аттрибута src для максимального изображения
	// регулярное изображение удаляет символы  _s из аттрибута src миниизображения


	if(!document.getElementById('bigimg')){
	var newim = new Image();
	// обработчик загрузки изображения
	attevent(newim,e)
	newim.src = e;
	}
}


$('.minislybox').click(function(){

	np = $(this)
	ajaxurl(np)
	return false
});

function ajaxurl(np)
{
	var imb = document.documentElement.clientWidth;
	var imbt = document.documentElement.clientHeight;
	offset = $(document).scrollTop();
	var centerimg = (imb/2) - (45/2); 
	var centerimgt = (imbt/2) - (41/2); 
	$("#slyload").css({'left': centerimg+"px", 'top': offset + centerimgt-10+"px"}).show();

	index = $(".minislybox").index($(".minislybox[active='active']"));
	$(".minislybox:eq("+index+")").attr('active','');
	$(np).attr('active','active');
	np = $(np).attr("src");

	n = np.replace(/_s/ig,"");


	bigimg(n)


}

// дополнение, которое не является обязательным, а создаёт лишь стиль для миниизображений
$('.minislybox').mouseover(function(){$(this).animate({'opacity' :'1'})});
$('.minislybox').mouseout(function(){$(this).animate({'opacity' :'0.5'});});

// функция обработчик запускаемая при возникновении события mouseover
// функция необходима для сокрытия элементов управления - кнопок next (prev)
function mouseoverhandler()
{

	var cont = $("#slyconteiner");
	var curimg = parseInt($(".minislybox").index($(".minislybox[active='active']"))) + 1;
	if(curimg == $(".minislybox").length && $(".minislybox").length !== 1)
	{
	$("#slyprev").fadeOut().css({'left':"10px",'top':((cont.height()-$("#slycomment").height()-6)/2)-16+"px","opacity":"0.6"}).fadeIn('slow');
	$("#slyprev").fadeOut().css({'left':"10px",'top':((cont.height()-$("#slycomment").height()-6)/2)-16+"px","opacity":"0.6"}).fadeIn('slow');
	}
	else if(curimg == 1 && $(".minislybox").length > 1){
	$("#slynext").fadeOut().css({'left':cont.width()-48+"px",'top':((cont.height()-$("#slycomment").height()-6)/2)-16+"px","opacity":"0.6"}).fadeIn('slow');
	}
	else if (curimg > 1 && curimg < $(".minislybox").length){
	$("#slyprev").fadeOut().css({'left':"10px",'top':((cont.height()-$("#slycomment").height()-6)/2)-16+"px","opacity":"0.6"}).fadeIn('slow');
	$("#slynext").fadeOut().css({'left':cont.width()-48+"px",'top':((cont.height()-$("#slycomment").height()-6)/2)-16+"px","opacity":"0.6"}).fadeIn('slow');
	}

}
// функция обработчик запускаемая при возникновении события mouseout
// функция необходима для отображения элементов управления - кнопок next (prev)
function mouseouthandler()
{
	$("#slynext,#slyprev").fadeOut();
}

function nextimg()
{
	$("#slyconteiner").fadeOut('slow',function(){
	$("#slyshadow").fadeOut('slow',function(){
	var e = $(".minislybox").index($(".minislybox[active='active']"))+1;
	if(e >= 0 && e < $(".minislybox").length+1) ajaxurl($(".minislybox:eq("+e+")"))
	});
	});
}

function previmg()
{
	$("#slyconteiner").fadeOut('slow',function(){
	$("#slyshadow").fadeOut('slow',function(){
	var e = $(".minislybox").index($(".minislybox[active='active']"))-1;
	if(e >= 0 && e < $(".minislybox").length+1) ajaxurl($(".minislybox:eq("+e+")"))
	});
	});
}

$("#slyclose").live('click',function(){delshadow()});
$("#slyprev").live('click',function(){previmg()});
$("#slynext").live('click',function(){nextimg()});

$("#slyconteiner").bind('mouseleave',mouseouthandler).bind('mouseenter',mouseoverhandler);


$("#slyprev,sly#next").live('click',function(){
	$("#slyconteiner").unbind('mouseleave',mouseouthandler);
	$("#slyconteiner").unbind('mouseenter',mouseoverhandler);
});

//удаление максимального изображения вместе с тенью
function delshadow()
{

	$("#slyconteiner").attr('terget','0');
	if(document.getElementById("slyshadow")){
	
	$("#slyconteiner").unbind('mouseleave',mouseouthandler);
	$("#slyconteiner").unbind('mouseenter',mouseoverhandler);
		// сокрытие багов прозрачного фильтра в ие
	
	$("#slynext,#slyprev").fadeOut('slow',function(){
	if(jQuery.browser.msie)	$("#slynext,#slyprev,#slyclose,#slyshadow").css('filter','');
		$('#slyconteiner').fadeOut('slow',function(){
		$('#slybigimg').remove();
		$('#slyshadow').fadeOut('slow');
		// сокрытие элементов управления
		$("#slyconteiner").hide();
		});
	// получение количества коментариев, находящихся внутри элемента div с целью их удаления
	});
	}
	else return false
}

jQuery.fn.fixPNG = function (element)
{
  //Если браузер IE версии 5.5-6
  if (/MSIE (5\.5|6).+Win/.test(navigator.userAgent))
  {
    var src;

    if (element.tagName=='IMG') //Если текущий элемент картинка (тэг IMG)
    {
      if (/\.png$/.test(element.src)) //Если файл картинки имеет расширение PNG
      {
        src = element.src;
        element.src = "/css/blank.gif"; //заменяем изображение прозрачным gif-ом
      }
    }
    else //иначе, если это не картинка а другой элемент
    {
	  //если у элемента задана фоновая картинка, то присваеваем значение свойства background-шmage переменной src
     src = element.css("background-image").match(/url\("(.+\.png)"\)/i);
      if (src)
      {
        src = src[1]; //берем из значения свойства background-шmage только адрес картинки
        element.css("background-image","none"); //убираем фоновое изображение
		
      }
    }
    //если, src не пуст, то нужно загрузить изображение с помощью фильтра AlphaImageLoader
    if (src) element.css("filter","progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + src + "',sizingMethod='image')");
  }
}



});



