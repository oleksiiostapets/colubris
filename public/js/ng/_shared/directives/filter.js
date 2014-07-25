/**
 * Created by alf on 7/25/14.
 */

'use strict';

app_module.directive('filter', function factory($q,$http, $templateCache,$compile,$window) {
    return function(scope,element,attrs) {

        //------------------------------------------------
        //
        //                 INPUTS
        //
        //------------------------------------------------

        var StatusField = function(scope,$compile,element) {
            var html = '<span>Status:</span><select ng-model="task_status" ng-change="Task.filter(\'status\',task_status)" ng-options="task_status.name for task_status in task_statuses"></select>';
            var template = angular.element(html);
            var linkFn = $compile(template);
            scope.filter.inputs = linkFn(scope);
            element.append(scope.filter.inputs);
        }
        StatusField(scope,$compile,element);

        var AssignedField = function(scope,$compile,element) {
            var html = '<span>Assigned:</span><select ng-model="assigned" ng-change="Task.filter(\'assigned_id\',assigned)" ng-options="assigned as combined(assigned) for assigned in assigneds"></select>';
            var template = angular.element(html);
            var linkFn = $compile(template);
            scope.filter.inputs = linkFn(scope);
            element.append(scope.filter.inputs);
        }
        AssignedField(scope,$compile,element);



    }
})
;