/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.controller(
    'inlineCrud',
    ['$scope','$document','$http','$rootScope','Client','Project','Quote',
        function ($scope,  $document,  $http,  $rootScope, Client, Project, Quote) {

            $rootScope.filter_values = {};

            // client
            $scope.client = {};
            $scope.Client = Client;
            $scope.clients = Client.client;

            Client.getFromServer();

            // projects
            $scope.Project = Project;
            $scope.projects = Project.projects;

            // Quote
            $scope.Quote = Quote;
            $scope.quotes = Quote.quotes;

            //clear form
            $scope.clearForm = function(){
                Client.cancel();
                Project.clear( 'projects.update' );
                Quote.clear( 'quotes.update' );
            };

            $scope.$on( 'client.update', function( event, args ) {
                $scope.client = args;
            });
            $scope.$on( 'clients.update', function( event ) {
                $scope.clients = Client.clients;
            });
            $scope.$on( 'projects.update', function( event ) {
                $scope.projects = Project.projects;
                if($scope.projects.length) {
                    Quote.getFromServerByProject($scope.projects[0]);
                }
            });
            $scope.$on( 'quotes.update', function( event ) {
                $scope.quotes = Quote.quotes;
            });
        }])
;