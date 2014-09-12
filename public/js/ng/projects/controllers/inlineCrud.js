'use strict';

app_module.controller(
    'inlineCrud',
            ['$scope','$document','$http', '$rootScope', 'Project', 'Quote', 'Client',
    function ($scope,  $document,  $http,  $rootScope,  Project, Quote, Client) {


        $rootScope.filter_values = {};
        $rootScope.items_on_page = 30;
        $rootScope.current_page = 1;
        $rootScope.paginator_max_shift = 10;

        //Project
        $scope.project ={};
        $scope.Project = Project;
        $scope.projects = Project.projects;
        Project.getFromServer();

        //Quote
        $scope.Quote = Quote;
        $scope.quotes = Quote.quotes;

        //Client
        $scope.Client = Client;
        $scope.clients = Client.clients;
        Client.getFromServer();


        //clear form
        $scope.clearForm = function(){
            Project.cancel();
            Quote.clear();
        };

        // ESC key close requirement div
        $(document).on('keydown', function(evt){
            evt = evt || window.event;
            if (evt.keyCode == 27) {
                $('#close-button').trigger('click');
            }
        });

        //events
        $scope.$on( 'projects.need_update', function( event, args ) {
            Project.getFromServer();
        });
        $scope.$on( 'project.update', function( event, args ) {
            $scope.project = args;
            if(!$scope.project.hasOwnProperty('id') && $scope.quotes.length){
                Quote.clear();
            }
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
        $scope.$on( 'quotes.update', function( event ) {
            $scope.quotes = Quote.quotes;
        });
        $scope.$on( 'clients.update', function( event ) {
            $scope.clients = Client.clients;
        });

    }]
);