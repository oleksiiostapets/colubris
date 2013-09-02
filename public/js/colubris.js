(function($){
$.colubris=function(){
    return $.colubris;
}
	
$.fn.extend({colubris:function(){
	var u=new $.colubris;
	u.jquery=this;
	return u;
}});
	
$.colubris._import=function(name,fn){
	$.colubris[name]=function(){
		var ret=fn.apply($.colubris,arguments);
		return ret?ret:$.colubris;
	}
}
	
$.each({
	alert: function(text){
		alert(text);		
	},
    toggle_is_included: function(url,reload_views){
        $(".toggle_is_included").each(function(i,e){
            $(e).bind("click",function(ev){
                ev.preventDefault();ev.stopPropagation();
                $(this).univ().ajaxec(url,{'req_id':$(this).attr('data-id')});
            });
        });
	},
    floating_total: function(el_id){

        var left = 50;
        var min_top = 20;
        var max_top = 250;
        var top = max_top;
        var window_top;
        var window_height;

        var el = $('#' + el_id);

        setCSS(el);

        $(window).scroll(function (event) {
            setCSS(el);
        });
        $(window).resize(function (event) {
            setCSS(el);
        });

        function setCSS(element) {
            window_top = $(window).scrollTop();
            window_height = $(window).height();

            if ( window_top < max_top ) {
                top = max_top - window_top;
                left = 50;
            } else if ( window_top > min_top ) {
                top = min_top;
                left = 160;
            }

            // if window is too small
            if ((window_height - 50) < top) {
                top = window_height - 50;
            }

            element
                    .css('position','fixed')
                    .css('top', top + 'px')
                    .css('right', left + 'px')
                    .css('z-index','5')
            ;
        }

	}
	
},$.colubris._import);
	
})(jQuery);