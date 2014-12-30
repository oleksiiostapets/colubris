/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.controller(
    'inlineCrud',
    ['$scope','$document','$http','Task','User','Project','Quote','Requirement', 'Time','$rootScope',
        function ($scope,  $document,  $http,  Task,  User,  Project,  Quote,  Requirement,   Time,  $rootScope) {

            $rootScope.filter_values = {};
            $rootScope.tasks_on_page = 30;
            $rootScope.current_page = 1;
            $rootScope.paginator_max_shift = 10;

            $scope.filter = {};

            $scope.Project = Project;
            $scope.projects = Project.projects;
            Project.getFromServer();

            $scope.Quote = Quote;
            $scope.quotes = Quote.quotes;

            $scope.Time = Time;
            $scope.times = Time.times;

            if(app_module.current_user_rights.indexOf('can_track_time') != -1){
                //Project.can_add_quote = 'display:block;';
            }else{
                Time.can_track_time = 'display:none;';
            }

            $scope.Requirement = Requirement;
            $scope.requirements = Requirement.requirements;

            $scope.User = User;
            $scope.requesters = User.users;
            User.getFromServer('requesters.update','getAllUsers');
            $scope.assigneds = User.users;
            User.getFromServer('assigneds.update','getAllUsers');

            $scope.task = {};
            $scope.Task = Task;
            $scope.tasks = Task.task;

            if(app_module.current_user_rights.indexOf('can_edit_task') != -1){
                //Project.can_edit_task = 'display:block;';
            }else{
                Task.can_edit_task = 'display:none;';
            }
            if(app_module.current_user_rights.indexOf('can_delete_task') != -1){
                //Project.can_delete_task = 'display:block;';
            }else{
                Task.can_delete_task = 'display:none;';
            }
            if(
                app_module.current_user_rights.indexOf('can_edit_task') == -1
                &&
                app_module.current_user_rights.indexOf('can_delete_task') == -1
            ){
                Task.show_actions = 'display:none;';
            }

            $scope.priorities = [
                {name:'low'},
                {name:'normal'},
                {name:'high'}
            ];
            $scope.priority = $scope.priorities;

            $scope.types = [
                {name:'Project',        value:'project'},
                {name:'Change request', value:'change_request'},
                {name:'Bug',            value:'bug'},
                {name:'Support',        value:'support'},
                {name:'Drop',           value:'drop'}
            ];
            $scope.type = $scope.types;

            $scope.statuses = [
                {name:'unstarted'},
                {name:'started'},
                {name:'finished'},
                {name:'tested'},
                {name:'rejected'},
                {name:'accepted'}
            ];
            $scope.status = $scope.statuses;

            $scope.task_statuses = Task.task_statuses;

            Task.getFromServer($rootScope.current_page);
            Task.getStatusesFromServer();

            //clear form
            $scope.clearForm = function(){
                Task.cancel();
            };
            $scope.$on( 'time.clear', function( event ) {
                $scope.times = {};
                $('#time_view textarea, #time_view input').val('');
            });

            $scope.$on( 'task.update', function( event, args ) {
                $scope.task = args;
            });
            $scope.$on( 'tasks.need_update', function( event, args ) {
                Task.getFromServer();
            });
            $scope.$on( 'tasks.update', function( event ) {
                $scope.tasks = Task.tasks;
                //console.log(Task.tasks);

                // Paginator
                var total_rows = Task.total_rows;
                var total_pages = Math.ceil(total_rows/$rootScope.tasks_on_page);
                $scope.paginators = [];
                for(var i=1; i<=total_pages; i++){
                    if($rootScope.current_page + $rootScope.paginator_max_shift >= i && $rootScope.current_page - $rootScope.paginator_max_shift <= i) {
                        var tmp_params = {};
                        if($rootScope.current_page == i){
                            tmp_params = {name:i, class:"ui-state-active ui-corner-all"};
                        }else{
                            tmp_params = {name:i};
                        }
                        $scope.paginators.push(tmp_params);
                    }
                }
                if ($rootScope.current_page > 1) $scope.prev_page = $rootScope.current_page - 1; else $scope.prev_page = $rootScope.current_page;
                if ($rootScope.current_page < total_pages) $scope.next_page = $rootScope.current_page + 1; else $scope.next_page = $rootScope.current_page;
                $scope.last_page = total_pages;
            });


            $scope.$on( 'projects.update', function( event ) {
                $scope.projects = Project.projects;
                if(angular.isDefined($scope.projects)){
                    $scope.projects.unshift({id:"",name:'all'});  //default element for filter by field
                }
            });

            $scope.$on( 'quotes.update', function( event ) {
                $scope.quotes = Quote.quotes;
                $scope.quotes.unshift({id:"",name:'all'})
            });

            $scope.$on( 'times.update', function( event ) {
                console.log('--->times.update');
                $scope.times = Time.times;
                $('#time_view textarea, #time_view input').val('');
                $('#time_view input[type=checkbox]').attr('checked',false);
            });
            $scope.$on( 'times.need_update', function( event, task ) {
                if(app_module.current_user_rights.indexOf('can_see_time') != -1){
                    Time.getFromServerByTask(task);
                }else{
                    $scope.$broadcast( 'times.update', task );
                }
            });

            $scope.$on( 'requirements.update', function( event ) {
                $scope.requirements = Requirement.requirements;
                $scope.requirements.unshift({id:"",name:'all'})
            });

            $scope.$on( 'task_statuses.update', function( event ) {
                $scope.task_statuses = Task.task_statuses;
            });

            $scope.$on( 'assigneds.update', function( event ) {
                $scope.assigneds = User.users;
                $scope.assigneds.unshift({id:"",email:'all'})
            });

            $scope.$on( 'requesters.update', function( event ) {
                $scope.requesters = User.users;
                $scope.requesters.unshift({id:"",email:'all'})
            });

            $scope.combined = function(user){
                if(user.name == undefined || user.name == ''){
                    return user.email;
                }
                else {
                    return user.name + " (" + user.email + ")";
                }
            }
        }]
);