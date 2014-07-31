'use strict';

app_module.controller(
    'inlineCrud',
            ['$scope','$document','$http','Project','$rootScope',
    function ($scope,  $document,  $http,  Project,  $rootScope) {

        $rootScope.items_on_page = 30;
        $rootScope.current_page = 1;
        $rootScope.paginator_max_shift = 10;

        $scope.Project = Project;
        $scope.projects = Project.projects;
        Project.getFromServer();

        $scope.$on( 'projects.update', function( event, args ) {
            //console.log(args);
            $scope.project = args;
        });
        $scope.$on( 'projects.update', function( event ) {
            $scope.projects = Project.projects;
            // Paginator
            var total_rows = Project.total_rows;
            var total_pages = Math.ceil(total_rows/$rootScope.items_on_page);
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

    }]
);