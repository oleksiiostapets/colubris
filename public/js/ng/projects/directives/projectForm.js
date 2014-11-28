/**
 * Created by Vladyslav Polyukhovych on 21.08.14.
 */

'use strict';

app_module.directive('projectForm', function factory($q,$http, $templateCache, $compile, $location) {
    return function(scope,element,attrs) {

        scope.$on( 'form.to_fixed_position', function( event, project) {
            element.addClass('fixed');

            scope.Quote.getFromServerByProject(project);

            scope.quote_url = app_module.base_url + app_module.prefix + 'quotes';
        });
        scope.$on( 'quotes.update', function( event, project ) {
            //select current client
            $("#project_client option").removeAttr("selected");
            $("#project_client option[value="+project.client_id+"]").attr("selected","selected");
        });
        scope.$on( 'form.to_regular_place', function( event ) {
            console.log('form.to_regular_place');
            element.removeClass('fixed');

            //clear client
            $("#project_client option").removeAttr("selected");
        });

        //save data
        scope.save = function(project,client){
            if(angular.isDefined(client)){
                project.client_id = client.id;
                project.client = client.name ;
            }
            scope.Project.save(project);
        };
    }
})
;