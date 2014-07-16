/**
 * Created by vadym on 7/15/14.
 */

'use strict';

app_module.directive('isIncluded',
            ['$q','$http', '$templateCache','$compile','$rootScope','Requirement',
    function( $q,  $http,   $templateCache,  $compile,  $rootScope,  Requirement) {

    function link(scope,element,attrs) {
        scope.rreqv.css = {};

        function update() {

            if (scope.rreqv.is_included == 1) {
                scope.rreqv.css.is_included = '☑';
                scope.rreqv.css.is_active   = 'active';
            } else {
                scope.rreqv.css.is_included = '☐';
                scope.rreqv.css.is_active   = 'not-active';
            }

            // get this form API
            scope.rreqv.can_toggle_requirement = 1;
            if (scope.rreqv.can_toggle_requirement == 1) {
                scope.rreqv.css.can_toggle  = 'can-toggle';
            } else {
                scope.rreqv.css.can_toggle  = '';
            }
        }
        update();

        scope.$on( 'checkbox.update.'+scope.rreqv.id, function( event, args ) {
            //console.log('$on checkbox.update.'+scope.rreqv.id);
            //console.log(args);
            if (args.is_included == 1) {
                args.is_included = 0;
            } else {
                args.is_included = 1;
            }
            update();

        });
    }
    return {
        link: link
    };

 }]);



//            $rootScope.$broadcast( 'requirements.update' );
//            $(Requirement.requirements).each(function(i,e){
//                console.log(scope.rreqv.id);
//                console.log(e.id);
//                if (e.id == scope.rreqv.id) {
//                    console.log(i);
//                    console.log(scope.rreqv.css);
//                    console.log(e.css);
//                    console.log(Requirement.requirements[i].css);
//                }
//            });


