/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.directive('requirementForm', function factory($q,$http, $templateCache,$compile) {
    return function(scope,element,attrs) {


        scope.$on( 'comments.update', function( event ) {
            console.log('comments.update');
            $('#comments-view textarea').val('');
        });

        scope.$on( 'form.to_fixed_position', function( event, reqv ) {
            console.log('form.to_fixed_position');
            element.addClass('fixed');

            if(reqv){
                scope.Comment.getFromServer(reqv.id);
                scope.Task.getFromServerByReqvId(reqv.id);
            }
            //console.log(scope.comments);
        });
        scope.$on( 'form.to_regular_place', function( event ) {
            console.log('form.to_regular_place');
            element.removeClass('fixed');
        });

        //save data
        scope.save = function(task,reqv){
            if(angular.isDefined(task.priority)){
                task.priority = task.priority.name;
            }
            if(angular.isDefined(task.type)){
                task.type = task.type.value;
            }
            if(angular.isDefined(task.status)){
                task.status = task.status.name;
            }
            if(angular.isDefined(task.requester)){
                task.requester_id = task.requester.id;
                task.requester = task.requester.name ;
            }
            if(angular.isDefined(task.assigned)){
                task.assigned_id = task.assigned.id;
                task.assigned = task.assigned.name ;
            }
            task.requirement_id = reqv.id ;
            //task.project_id = reqv.project_id ; //No need anymore
            scope.Task.save(task);
        };
    }
})
;