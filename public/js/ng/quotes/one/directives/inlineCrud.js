/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.directive('inlineCrud', function factory($q,$http, $templateCache,$compile) {
    return function(scope,element,attrs) {

        scope.form = app_module.base_url + 'js/ng/quotes/one/templates/form.html';
        scope.crud = app_module.base_url + 'js/ng/quotes/one/templates/crud.html';
        scope.comment_list = app_module.base_url + 'js/ng/quotes/one/templates/comment-list.html';
        scope.task_list = app_module.base_url + 'js/ng/quotes/one/templates/task-list.html';
        scope.info = app_module.base_url + 'js/ng/quotes/one/templates/info.html';


        /*var addReorderButton = function(scope,$compile,element) {
            var html = '<span class="inline-edit" editable-date="quote[0].deadline_obj" onaftersave="Quote.save(quote[0],projects[0])">{{quote[0].deadline || "---" }}</span>';
            var template = angular.element(html);
            var linkFn = $compile(template);
            scope.can_edit_deadline = linkFn(scope);
            element.append(scope.can_edit_deadline);
        };*/

        /*var html = '<span class="inline-edit" editable-date="quote[0].deadline_obj" onaftersave="Quote.save(quote[0],projects[0])">{{quote[0].deadline || "---" }}</span>';
        var template = angular.element(html);
        var linkFn = $compile(template);
        scope.can_edit_deadline = linkFn(scope);
        element.append(scope.can_edit_deadline);*/

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