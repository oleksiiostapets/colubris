/**
 * Created by Vladyslav Polyukhovych on 21.08.14.
 */

'use strict';

app_module.directive('taskForm', function factory($q,$http, $templateCache, $compile, $location) {
    return function(scope,element,attrs) {

        scope.$on( 'form.to_fixed_position', function( event, task) {
            element.addClass('fixed');

            if(task){
                if(app_module.current_user_rights.indexOf('can_see_time') != -1){
                    scope.Time.getFromServerByTask(task);
                }
            }

            scope.task_url = app_module.base_url + app_module.prefix + 'tasks';
        });
        scope.$on( 'task.update', function( event, task ) {

            //select current selectors
            $("#task_priority option").removeAttr("selected");
            $("#task_priority option[value="+task.priority+"]").attr("selected","selected");
            $("#task_type option").removeAttr("selected");
            $("#task_type option[value="+task.type+"]").attr("selected","selected");
            $("#task_status option").removeAttr("selected");
            $("#task_status option[value="+task.status+"]").attr("selected","selected");
            $("#task_requester option").removeAttr("selected");
            $("#task_requester option[value="+task.requester_id+"]").attr("selected","selected");
            $("#task_assigned option").removeAttr("selected");
            $("#task_assigned option[value="+task.assigned_id+"]").attr("selected","selected");
        });
        scope.$on( 'form.to_regular_place', function( event ) {
            console.log('form.to_regular_place');
            element.removeClass('fixed');
        });

        //save data
        scope.save = function(task,priority,type,status,requester,assigned){
            if(angular.isDefined(priority.name)){
                task.priority = priority.name;
            }
            if(angular.isDefined(type.value)){
                task.type = type.value;
            }
            if(angular.isDefined(status.name)){
                task.status = status.name;
            }
            if(angular.isDefined(requester)){
                task.requester_id = requester.id;
                task.requester = requester.name ;
            }
            if(angular.isDefined(assigned)){
                task.assigned_id = assigned.id;
                task.assigned = assigned.name ;
            }
            scope.Task.save(task);
        };
    }
})
;