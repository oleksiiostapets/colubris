/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.directive('inlineCrud', function factory($q,$http, $templateCache,$compile) {
    return function(scope,element,attrs) {

        scope.form = app_module.base_url + 'js/ng/clients/templates/form.html';
        scope.crud = app_module.base_url + 'js/ng/clients/templates/crud.html';
        scope.project_list = app_module.base_url + 'js/ng/clients/templates/project-list.html';
    }
})
;