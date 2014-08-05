/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.directive('inlineCrud', function factory($q,$http, $templateCache,$compile) {
    return function(scope,element,attrs) {

        scope.form = app_module.base_url + 'js/ng/users/templates/form.html';
        scope.crud = app_module.base_url + 'js/ng/users/templates/crud.html';
        scope.right_list = app_module.base_url + 'js/ng/users/templates/right-list.html';
//        scope.right_form = app_module.base_url + 'js/ng/users/templates/right-form.html';
    }
})
;