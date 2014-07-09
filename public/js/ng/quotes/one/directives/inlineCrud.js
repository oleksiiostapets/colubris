/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.directive('inlineCrud', function factory($q,$http, $templateCache,$compile) {
    return function(scope,element,attrs) {

        //------------------------------------------------
        //
        //                 PANNEL
        //
        //------------------------------------------------
//        var addGrid = function(scope,$compile,element) {
//            var html = '<ul>' +
//                '<li>bla</li>' +
//                '<li ng-repeat="reqv in requirements track by $index">' +
//                    '<span>{{$index}}</span> | ' +
//                    '<span>{{reqv.id}}</span> | ' +
//                    '<span>{{reqv.name}}</span> | ' +
//                    '<span>{{reqv.quote}}</span> | ' +
//                    '<span>{{reqv.user}}</span> | ' +
//                    '<span>{{reqv.descr}}</span> | ' +
//                    '<span>{{reqv.estimate}}</span> | ' +
//                    '<span>{{reqv.is_included}}</span> | ' +
//                    '<span>{{reqv.is_deleted}}</span> | ' +
//                    '<span>{{reqv.project_id}}</span> | ' +
//                    '[ <a href="" ng-click="Requirement.edit($index)">E</a> ]' +
//                    '[ <a href="" ng-click="Requirement.remove($index)">X</a> ]' +
//                '</li>' +
//            '</ul>';
//            var template = angular.element(html);
//            var linkFn = $compile(template);
//            scope.actionButtonSet.button_get_data = linkFn(scope);
//            element.append(scope.actionButtonSet.button_get_data);
//        }
//
//        addGrid(scope,$compile,element);
        // PANNEL ----------------------------------------

    }
})
;