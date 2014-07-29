/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.controller(
    'inlineCrud',
            ['$scope','$document','$http','Task','User','TaskStatus','Project','Quote','Requirement','$rootScope',
    function ($scope,  $document,  $http,  Task,  User,  TaskStatus,  Project,  Quote,  Requirement,  $rootScope) {

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

        $scope.Requirement = Requirement;
        $scope.requirements = Requirement.requirements;

        $scope.User = User;
        $scope.assigneds = User.users;
        User.getFromServer('assigneds.update');

        $scope.TaskStatus = TaskStatus;
        $scope.task_statuses = TaskStatus.task_statuses;
        TaskStatus.getFromServer();

        $scope.task = {};
        $scope.Task = Task;
        $scope.tasks = Task.task;

        Task.getFromServer($rootScope.current_page);

        $scope.$on( 'task.update', function( event, args ) {
            $scope.task = args;
        });
        $scope.$on( 'tasks.update', function( event ) {
            $scope.tasks = Task.tasks;

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
        });

        $scope.$on( 'quotes.update', function( event ) {
            $scope.quotes = Quote.quotes;
        });

        $scope.$on( 'requirements.update', function( event ) {
            $scope.requirements = Requirement.requirements;
        });

        $scope.$on( 'task_statuses.update', function( event ) {
            $scope.task_statuses = TaskStatus.task_statuses;
        });

        $scope.$on( 'assigneds.update', function( event ) {
            $scope.assigneds = User.users;
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