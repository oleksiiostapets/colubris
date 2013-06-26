$(function () {

	// dotdotdot
	$('.media .media-body .media-heading, .media .media-body p').dotdotdot({
		height: '65px',
		watch: 'true'
	});

    $.scrollUp({
    	scrollImg: true,
    });
    $('#scrollUp').append("<span></span>");
	$('.page-title-wrapper.fixed').height($(".page-title").outerHeight());
    $('.page-title-wrapper.fixed .page-title').affix({
        offset: {
            top: function() {
                return $('.page-title').position().top;
            }
        }
    });
	$('.nav-categories .nav a').on('click', function(){
	    $(".nav-categories .btn-navbar").click();
	});
    var $fa=$('.font-adjust');
    $fa.on('click','.bigger',function(){
        var fontSize = parseInt($(".section.full-article").css("font-size"));
        if(!fontSize)fontSize=16;
        if(fontSize<28)fontSize+=4;
        $('.section.full-article').css({'font-size': fontSize});
    });
    $fa.on('click','.smaller',function(){
        var fontSize = parseInt($(".section.full-article").css("font-size"));
        if(!fontSize)fontSize=16;
        if(fontSize>8)fontSize-=4;
        $('.section.full-article').css({'font-size': fontSize});
    });

	// Share Article
	$('#article-share').popover({
	    html: true,
	    title: '',
	    content: $('#popover-share').html(),
	    placement: 'bottom'
	});

	$('#article-share').click(function (e) {
		$(this).addClass("active");
	    e.stopPropagation();
	});

	$(document).click(function (e) {
	    if (($('.popover').has(e.target).length == 0) || $(e.target).is('.close')) {
	        $('#article-share').popover('hide').removeClass("active");
	    }
	});

});

var r=function(e){
    var w = $('#main').width();

    var h=$('.page-title');
        h.css({width:$('#main').width()});
}

$(window).resize(r);
$(r);
