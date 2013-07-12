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

    myredirect: function(field,par,url){
        var val = $('#'+field).val();
        var ajUrl =
            (url.indexOf("?") === -1)?
                    url + '?'+par+'='+val:
                    url + '&'+par+'='+val;
        window.location.replace(ajUrl);
    }

},$.colubris._import);

})(jQuery);