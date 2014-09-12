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

            //project selection
            $scope.projectSelect = function(project, event){
                Quote.getFromServerByProject(project);
                $rootScope.$broadcast( 'project.selected' );
            };

            // Quote
            $scope.Quote = Quote;
            $scope.quotes = Quote.quotes;

            //clear form
            $scope.clearForm = function(){
                Client.cancel();
                Project.clear( 'projects.update' );
                Quote.clear( 'quotes.update' );
            };

            // ESC key close requirement div
            $(document).on('keydown', function(evt){
                evt = evt || window.event;
                if (evt.keyCode == 27) {
                    $('#close-button').trigger('click');
                }
            });

            $scope.$on( 'clients.need_update', function( event, args ) {
                Client.getFromServer();
            });
            //avatar upload
            $scope.upload = function() {
                var url = app_module.base_url + app_module.prefix  + 'api/client/' + 'addToFilestore' + app_module.postfix;
                var fd = new FormData();
                fd.append('file',document.getElementById('file').files[0]);
                fd.append('id',$scope.client.id);
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4) {
                        try {
                            var obj = angular.fromJson(xhr.responseText);
                        } catch (e) {
                            alert('Error! No data received.');
                        }
                        if (obj.result === 'success') {
                            $("#avatar").html('<img src="http://' + document.domain + obj.data.thumb_url + '" alt="Avatar" />');
                        } else {
                            alert('Error! No success message received.');
                        }
                    }
                }
                xhr.open("POST", url);
                xhr.send(fd);
            };

            $scope.$on( 'client.update', function( event, args ) {
                $scope.client = args;
                $scope.showAvatar = !$scope.client.id=='';
                if($scope.client.avatar_thumb != '' && $scope.client.avatar_thumb != null){//TODO: change image setter to ng directives
                    $("#avatar").html('<img src="http://' + document.domain + window.location.pathname + 'upload/' + $scope.client.avatar_thumb + '" alt="Avatar" />');
                }else{
                    $("#avatar").html('');
                }
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
                $rootScope.$broadcast( 'project.selected' );
            });
        }])
;