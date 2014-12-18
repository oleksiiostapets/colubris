'use strict';

app_module.directive('inlineCrud', function factory($q,$http, $templateCache,$compile) {
    return function(scope,element,attrs) {

        scope.form = app_module.base_url + 'js/ng/projects/templates/form.html';
        scope.crud = app_module.base_url + 'js/ng/projects/templates/crud.html';
        scope.quote_list = app_module.base_url + 'js/ng/projects/templates/quote-list.html';
        scope.participant_list = app_module.base_url + 'js/ng/projects/templates/participant-list.html';
    }
})
;