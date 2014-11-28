/**
 * Created by Vladyslav Polyukhovych on 21.08.14.
 */

'use strict';

app_module.directive('taskForm', function factory($q,$http, $templateCache, $compile, $location) {
    return function(scope,element,attrs) {

        scope.$on( 'form.to_fixed_position', function( event, task) {
            element.addClass('fixed');

            //scope.Quote.getFromServerByProject(project);

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
            //console.log('form.to_regular_place');
            element.removeClass('fixed');
        });
    }
})
;