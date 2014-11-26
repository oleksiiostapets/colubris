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

        var ProjectField = function(scope,$compile,element) {
            var html = '<div class="atk-col-2 atk-cells"><div class="atk-cell atk-text-nowrap"><label class="atk-input-label"><strong>Project:</strong></label></div><div class="atk-cell atk-jackscrew"><select class="atk-jackscrew" ng-model="project" ng-change="Quote.getFromServerByProject(project);Requirement.clear();Task.filter(\'project_id\',project);User.getFromServerByProject(project);" ng-options="project.name for project in projects"></select></div></div>';
            var template = angular.element(html);
            var linkFn = $compile(template);
            scope.filter.inputs = linkFn(scope);
            element.append(scope.filter.inputs);
        };
        ProjectField(scope,$compile,element);

        var QuoteField = function(scope,$compile,element) {
            var html = '<div class="atk-col-3 atk-cells"><div class="atk-cell atk-text-nowrap"><label class="atk-input-label"><strong>Quote:</strong></label></div><div class="atk-cell atk-jackscrew"><select class="atk-jackscrew" ng-model="quote" ng-change="Requirement.getFromServerByQuote(quote);Task.filter(\'quote_id\',quote)" ng-options="quote.name for quote in quotes"></select></div></div>';
            var template = angular.element(html);
            var linkFn = $compile(template);
            scope.filter.inputs = linkFn(scope);
            element.append(scope.filter.inputs);
        };
        QuoteField(scope,$compile,element);

        var RequirementField = function(scope,$compile,element) {
            var html = '<div class="atk-col-3 atk-cells"><div class="atk-cell atk-text-nowrap"><label class="atk-input-label"><strong>Requirement:</strong></label></div><div class="atk-cell atk-jackscrew"><select class="atk-jackscrew" ng-model="requirement" ng-change="Task.filter(\'requirement_id\',requirement)" ng-options="requirement.name for requirement in requirements"></select></div></div>';
            var template = angular.element(html);
            var linkFn = $compile(template);
            scope.filter.inputs = linkFn(scope);
            element.append(scope.filter.inputs);
        };
        RequirementField(scope,$compile,element);

        var StatusField = function(scope,$compile,element) {
            var html = '<div class="atk-col-2 atk-cells"><div class="atk-cell atk-text-nowrap"><label class="atk-input-label"><strong>Status:</strong></label></div><div class="atk-cell atk-jackscrew"><select class="atk-jackscrew" ng-model="task_status" ng-change="Task.filter(\'status\',task_status)" ng-options="task_status.name for task_status in task_statuses"></select></div></div>';
            var template = angular.element(html);
            var linkFn = $compile(template);
            scope.filter.inputs = linkFn(scope);
            element.append(scope.filter.inputs);
        };
        StatusField(scope,$compile,element);

        var AssignedField = function(scope,$compile,element) {
            var html = '<div class="atk-col-2 atk-cells"><div class="atk-cell atk-text-nowrap"><label class="atk-input-label"><strong>Assigned:</strong></label></div><div class="atk-cell atk-jackscrew"><select class="atk-jackscrew" ng-model="assigned" ng-change="Task.filter(\'assigned_id\',assigned)" ng-options="assigned as combined(assigned) for assigned in assigneds"></select></div></div>';
            var template = angular.element(html);
            var linkFn = $compile(template);
            scope.filter.inputs = linkFn(scope);
            element.append(scope.filter.inputs);
        };
        AssignedField(scope,$compile,element);
    }
})
;