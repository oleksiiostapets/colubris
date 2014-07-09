/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.directive('buttonSet', function factory($q,$http, $templateCache,$compile,$window) {
    return function(scope,element,attrs) {



        //------------------------------------------------
        //
        //                 PANNEL
        //
        //------------------------------------------------

        var fixPannel = function(element) {
            element.css('top',$($window).scrollTop() + 10);
        }
        angular.element(document).bind('scroll', function () {
            fixPannel(element);
        });
        // PANNEL ----------------------------------------






        //------------------------------------------------
        //
        //                 BUTTONS
        //
        //------------------------------------------------

//        var addGetDataButton = function(scope,$compile,element) {
//            var html = '<input type="button" ng-click="Requirement.getFromServer()" class="button" value="Get Data" />';
//            var template = angular.element(html);
//            var linkFn = $compile(template);
//            scope.actionButtonSet.button_get_data = linkFn(scope);
//            element.append(scope.actionButtonSet.button_get_data);
//        }
        var addReorderButton = function(scope,$compile,element) {
            var html = '<input type="button" ng-click="myData.doClick()" class="button" value="Reorder" />';
            var template = angular.element(html);
            var linkFn = $compile(template);
            scope.actionButtonSet.button_get_data = linkFn(scope);
            element.append(scope.actionButtonSet.button_get_data);
        }
//        addGetDataButton(scope,$compile,element);
//        addReorderButton(scope,$compile,element);
        // BUTTONS ------------------------------------------



    }
})
;