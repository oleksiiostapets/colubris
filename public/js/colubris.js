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
	roleMenuClick: function(id, url, data){
        $('#' + id).bind('click',function(ev) {
            ev.preventDefault();ev.stopPropagation();
            //alert(id);
            $(this).univ().ajaxec(url, data);
        });
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

	},
    reloadForm: function (form_id,field) {
        var queue = ['project','quote','requirement'];
        var form = $("#" + form_id).find("form");
        var action = form.attr("action");

        var remove = false;
        $.each(queue,function(count,word) {
            if (!remove) {
                form.find('select').each(function(c,el) {
                    if ($(el).attr('data-shortname') == word) {
                        action = action + '&' + $(el).attr('data-shortname') + '=' + $(el).val();
                    }
                });
                if (word == field) {
                    remove = true;
                }
            } else {
                $rr = form.find('select[data-shortname="'+ word +'"]').closest('.atk-form-row');
                $rr.remove();
                //console.log($rr);
            }

        });
        form.attr("action",action);
        //console.log("============ action ============");
        //console.log(action);
        form.submit();

    },
    startRequirementApp: function(quote_id,base_url,prefix,postfix,api_base_url,lhash) {
        angular.element(document).ready(function() {
            app_module.quote_id = quote_id;
            app_module.base_url = base_url;
            app_module.prefix   = prefix;
            app_module.postfix  = postfix;
            app_module.api_base_url = api_base_url;
            app_module.lhash  = lhash;
            angular.bootstrap(document, ['quotes.one.app']);
        });
    },
    startClientsApp: function(base_url,prefix,postfix,api_base_url,lhash) {
        angular.element(document).ready(function() {
//            app_module.client_id = client_id;
            app_module.base_url = base_url;
            app_module.prefix   = prefix;
            app_module.postfix  = postfix;
            app_module.api_base_url = api_base_url;
            app_module.lhash  = lhash;
            angular.bootstrap(document, ['clients.app']);
        });
    },
    startTasksApp: function(base_url,prefix,postfix,api_base_url,lhash) {
        angular.element(document).ready(function() {
            app_module.base_url = base_url;
            app_module.prefix   = prefix;
            app_module.postfix  = postfix;
            app_module.api_base_url = api_base_url;
            app_module.lhash  = lhash;
            angular.bootstrap(document, ['tasks.app']);
        });
    },
    startSettingsApp: function(user_id,base_url,prefix,postfix,api_base_url,lhash) {
        angular.element(document).ready(function() {
            app_module.user_id  = user_id;
            app_module.base_url = base_url;
            app_module.prefix   = prefix;
            app_module.postfix  = postfix;
            app_module.api_base_url = api_base_url;
            app_module.lhash  = lhash;
            angular.bootstrap(document, ['settings.app']);
        });
    },
    startProjectsApp: function(base_url,prefix,postfix,api_base_url,lhash) {
        angular.element(document).ready(function() {
            app_module.base_url = base_url;
            app_module.prefix   = prefix;
            app_module.postfix  = postfix;
            app_module.api_base_url = api_base_url;
            app_module.lhash  = lhash;
            angular.bootstrap(document, ['projects.app']);
        });
    },
    startUsersApp: function(base_url,prefix,postfix) {
        angular.element(document).ready(function() {
            app_module.base_url = base_url;
            app_module.prefix   = prefix;
            app_module.postfix  = postfix;
            angular.bootstrap(document, ['users.app']);
        });
    }
},$.colubris._import);

})(jQuery);