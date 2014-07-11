/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.directive('requirementForm', function factory($q,$http, $templateCache,$compile) {
    return function(scope,element,attrs) {



        scope.$on( 'form.to_fixed_position', function( event, reqv ) {
            console.log('form.to_fixed_position');
            element.addClass('fixed');
            console.log(reqv);

            scope.Comment.getFromServer(reqv.id);
            console.log(scope.comments);
        });
        scope.$on( 'form.to_regular_place', function( event ) {
            console.log('form.to_fixed_position');
            element.removeClass('fixed');
        });

        //------------------------------------------------
        //
        //                 PANNEL
        //
        //------------------------------------------------
//        var addForm = function(scope,$compile,element) {
//            var html = '<form>' +
//                    'Id<input type="text" ng-model="reqv.id" />' +
//                    'Name<input type="text" ng-model="reqv.name" />' +
//                    '<button ng-click="Requirement.add(reqv)" class="atk-button-small atk-button" value="Add">Add</button>' +
//                    '</form>';
//            var template = angular.element(html);
//            var linkFn = $compile(template);
//            scope.actionButtonSet.button_get_data = linkFn(scope);
//            element.append(scope.actionButtonSet.button_get_data);
//        }
//        addForm(scope,$compile,element);
        // PANNEL ----------------------------------------

    }
})
;