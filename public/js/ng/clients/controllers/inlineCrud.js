/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.controller(
    'inlineCrud',
            ['$scope','$document','$http','Client',
    function ($scope,  $document,  $http,  Client) {


    // client
    $scope.client = {};
    $scope.Client = Client;
    $scope.clients = Client.client;


    Client.getFromServer();

    $scope.$on( 'client.update', function( event, args ) {
        $scope.client = args;
    });
    $scope.$on( 'clients.update', function( event ) {
        $scope.clients = Client.clients;
    });
}])
;