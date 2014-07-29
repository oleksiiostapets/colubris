/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.directive('clientForm', function factory($q,$http, $templateCache,$compile) {
    return function(scope,element,attrs) {


        scope.$on( 'form.to_fixed_position', function( event, client ) {
            console.log('form.to_fixed_position');
            element.addClass('fixed');
            console.log(client);

            scope.Client.getFromServer(client.id);
            scope.Project.getFromServer('client_id',client.id);
        });
        scope.$on( 'form.to_regular_place', function( event ) {
            console.log('form.to_fixed_position');
            element.removeClass('fixed');
        });
    }
})
;