/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.directive('clientForm', function factory($q,$http, $templateCache, $compile, $location) {
    return function(scope,element,attrs) {


        scope.formAvatar = app_module.base_url + 'js/ng/clients/templates/formAvatar.html';

        scope.$on( 'form.to_fixed_position', function( event, client, project ) {
            console.log('form.to_fixed_position');
            element.addClass('fixed');
            //console.log(client);
            //console.log('===========' + project);//TODO undefined

            if(client){
                scope.Client.getFromServer(client.id);
                scope.Project.getFromServer('client_id',client.id);
            }
            if(project) {
                scope.Quote.getFromServerByProject(project);
            }
            scope.quote_url = app_module.base_url + app_module.prefix + 'quotes';
        });
        scope.$on( 'form.to_regular_place', function( event ) {
            console.log('form.to_fixed_position');
            element.removeClass('fixed');
        });
        scope.$on( 'project.selected',function(){//TODO make global
            //set class 'active' to clicked element
            if( (event.currentTarget.hasOwnProperty('classList') && $.inArray('default',event.currentTarget.classList)) || event.currentTarget.className==='default'){
                $('.selectable-list .active').removeClass('active').addClass('default');
                $(event.currentTarget).removeClass('default').addClass('active');
            }else{
            //default set class 'active' to first element
                if(!$('.selectable-list .active').length && $('.selectable-list .default').length){
                    $($('.selectable-list .default')[0]).removeClass('default').addClass('active');
                }
            }
        });
    }
})
;