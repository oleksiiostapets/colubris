/**
 * Created by vadym on 7/15/14.
 */

'use strict';

app_module.directive('isIncluded', function factory($q,$http, $templateCache,$compile,Requirement) {
    return function(scope,element,attrs) {

        console.log($templateCache);
        scope.rreqv.css = {};


        element.find('.toggle_is_included').on('click',function(e) {
            console.log(scope.rreqv);
            if (scope.rreqv.is_included == 1) {
                scope.rreqv.is_included = 0;
            } else {
                scope.rreqv.is_included = 1;
            }
            //$(this).css('border','1px red solid');

            //scope.$broadcast('checkbox.update',scope.rreqv);
            update();
        });

        function update() {
            console.log('update');
            console.log(scope.rreqv);
            //console.log(scope.rreqv);

            if (scope.rreqv.is_included == 1) {
                scope.rreqv.css.is_included = '☑';
                scope.rreqv.css.is_active   = 'active';
            } else {
                scope.rreqv.css.is_included = '☐';
                scope.rreqv.css.is_active   = 'not-active';
            }

            // get this form API
            scope.rreqv.can_toggle_requirement = 1;
            if (scope.rreqv.can_toggle_requirement == 1) {
                scope.rreqv.css.can_toggle  = 'can-toggle';
            } else {
                scope.rreqv.css.can_toggle  = '';
            }
            //console.log(scope.rreqv);
        }
        update();

//        scope.$on( 'checkbox.update', function( event ) {
//            console.log(scope.rreqv);
//            update();
//        });
    }
});
//app_module.directive('isIncludedCheckbox', function factory($q,$http, $templateCache,$compile) {
//    return function(scope,element,attrs) {
//
//        element.on('click',function(e) {
//            //console.log(scope.rreqv);
//            if (scope.rreqv.is_included == 1) {
//                scope.rreqv.is_included = 0;
//            } else {
//                scope.rreqv.is_included = 1;
//            }
//            //$(this).css('border','1px red solid');
//
//            scope.$broadcast('checkbox.update',scope.rreqv);
//        });
//
//    }
//});