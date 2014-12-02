/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.directive('inlineCrud', function factory($q,$http, $templateCache,$compile) {
    return function(scope,element,attrs) {

        scope.form = app_module.base_url + 'js/ng/tasks/templates/form.html';
        scope.crud = app_module.base_url + 'js/ng/tasks/templates/crud.html';
        scope.time_list = app_module.base_url + 'js/ng/tasks/templates/time-list.html';
        //scope.spent_time_field = app_module.base_url + 'js/ng/tasks/templates/spent-time.html';
        //scope.spent_time_field_editable = app_module.base_url + 'js/ng/tasks/templates/spent-time-editable.html';
    }
})
;