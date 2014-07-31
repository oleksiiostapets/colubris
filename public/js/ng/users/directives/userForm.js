/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.directive('userForm', function factory($q,$http, $templateCache,$compile) {
    return function(scope,element,attrs) {


        scope.$on( 'form.to_fixed_position', function( event, user ) {
            console.log('form.to_fixed_position');
            element.addClass('fixed');

            scope.User.getFromServer(user.id);
        });
        scope.$on( 'form.to_regular_place', function( event ) {
            console.log('form.to_fixed_position');
            element.removeClass('fixed');
        });
    }
})
;