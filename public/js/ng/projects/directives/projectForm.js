/**
 * Created by Vladyslav Polyukhovych on 21.08.14.
 */

'use strict';

app_module.directive('projectForm', function factory($q,$http, $templateCache, $compile, $location) {
    return function(scope,element,attrs) {

        scope.$on( 'form.to_fixed_position', function( event, project) {
            element.addClass('fixed');

            scope.Quote.getFromServerByProject(project);

            if(app_module.current_user_rights.indexOf('can_manage_participants') != -1){
                scope.Participant.getFromServerByProject(project);
                scope.can_see_participant_html = 'display:block;';
            }else{
                scope.can_see_participant_html = 'display:none;';
            }

            if(app_module.current_user_rights.indexOf('can_see_finance') != -1){
                //user can see money
                scope.can_see_finance = 'display:block;';
            }
            else{
                scope.can_see_finance = 'display:none;';
            }
            if(app_module.current_user_rights.indexOf('can_see_spent_time') != -1){
                //user can see spent time
                scope.can_see_spent_time = 'display:block;';
            }else{
                scope.can_see_spent_time = 'display:none;';
            }

            scope.quote_url = app_module.base_url + app_module.prefix + 'quotes';
        });
        scope.$on( 'participants.need_update', function( event ) {
            $('#participant_view select').val('');
        });
        scope.$on( 'quotes.update', function( event, project ) {
            //select current client
            //$("#project_client option").removeAttr("selected");
            //if(project){
            //    $("#project_client option[value="+project.client_id+"]").attr("selected","selected");
            //}
        });
        scope.$on( 'form.to_regular_place', function( event ) {
            console.log('form.to_regular_place');
            element.removeClass('fixed');

            //clear client
            //$("#project_client option").removeAttr("selected");
        });

        //save project data
        scope.save = function(project,client){
            //if(angular.isDefined(client)){
            //    project.client_id = client.id;
            //    project.client = client.name ;
            //}
            scope.Project.save(project);
        };
    }
})
;