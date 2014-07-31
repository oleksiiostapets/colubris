'use strict';

app_module.directive('inlineCrud', function factory($q,$http, $templateCache,$compile) {
    return function(scope,element,attrs) {

        scope.crud = app_module.base_url + 'js/ng/projects/templates/crud.html';
    }
})
;