/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.controller(
    'inlineCrud',
            ['$scope','$document','$http','Client','Project',
    function ($scope,  $document,  $http,  Client, Project) {


    // client
    $scope.client = {};
    $scope.Client = Client;
    $scope.clients = Client.client;

    Client.getFromServer();

    // projects
    $scope.Project = Project;
    $scope.projects = Project.projects;

    $scope.$on( 'client.update', function( event, args ) {
        $scope.client = args;
    });
    $scope.$on( 'clients.update', function( event ) {
        $scope.clients = Client.clients;
    });
    $scope.$on( 'projects.update', function( event ) {
        $scope.projects = Project.projects;
    });
}])
;