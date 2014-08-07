/**
 * Created by konstantin on 06.08.14.
 */

/*app_module.filter('checked', function() {
    return function(input) {
        var out;
        if (input) {
            out = ' checked="checked" ';
        } else {
            out = '';
        }
        return out;
    };
})*/



'use strict';

app_module.directive('isChecked', function factory($q,$http, $templateCache,$compile) {
    console.log('-----> isChecked');
    return function (scope, element, attrs) {
            if (attrs.shouldCheck === 'true') {
                attrs.$set(scope.attr || "checked", true);
            } else {
                attrs.$set(scope.attr || "checked", false);
            }
        }
    ;
})
;