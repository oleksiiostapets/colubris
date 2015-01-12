'use strict';

app_module.controller(
    'inlineCrud',
    ['$scope','$document','$http', '$rootScope', 'Project', 'Quote', 'Client', 'Participant', 'User', 'Right', '$filter',
        function ($scope,  $document,  $http,  $rootScope,  Project, Quote, Client, Participant, User, Right, $filter) {

            $rootScope.filter_values = {};
            $rootScope.items_on_page = 30;
            $rootScope.current_page = 1;
            $rootScope.paginator_max_shift = 10;

            //Project
            $scope.project ={};
            $scope.Project = Project;
            $scope.projects = Project.projects;
            Project.getFromServer();
            if(app_module.current_user_rights.indexOf('can_add_projects') != -1){
                //Project.can_add_projects = 'display:block;';
            }else{
                Project.can_add_projects = 'display:none;';
            }
            if(app_module.current_user_rights.indexOf('can_edit_projects') != -1){
                //Project.can_edit_projects = 'display:block;';
            }else{
                Project.can_edit_projects = 'display:none;';
            }
            if(app_module.current_user_rights.indexOf('can_delete_projects') != -1){
                //Project.can_delete_projects = 'display:block;';
            }else{
                Project.can_delete_projects = 'display:none;';
            }
            if(
                app_module.current_user_rights.indexOf('can_edit_projects') == -1
                &&
                app_module.current_user_rights.indexOf('can_delete_projects') == -1
            ){
                Project.show_actions = 'display:none;';
            }

            //Quote
            $scope.Quote = Quote;
            $scope.quotes = Quote.quotes;

            if(app_module.current_user_rights.indexOf('can_add_quote') != -1){
                //Project.can_add_quote = 'display:block;';
            }else{
                Project.can_add_quote = 'display:none;';
            }

            // Quote status
            $scope.statuses = Quote.getStatuses('value','text');
            $scope.showStatus = function(quote) {
                var selected = $filter('filter')($scope.statuses, {value: quote.status});
                return (quote.status && selected.length) ? selected[0].text : 'Not set';
            };





            //Participants
            $scope.Participant = Participant;
            $scope.participants = Participant.participants;

            //User
            /*$scope.User = User;
            $scope.users = User.users;
            User.getFromServer('users.update','getAllUsers');
            $scope.$on( 'users.update', function( event ) {
                $scope.users = User.users;
                $scope.users.unshift({id:"",email:'all'})
            });*/
            $scope.$on( 'participants.need_update', function( event, project ) {
                Participant.getFromServerByProject(project);
            });

            //Right
            $scope.Right = Right;
            $scope.rights = Right.rights;

            //Client
            //$scope.Client = Client;
            //$scope.clients = Client.clients;
            //Client.getFromServer();


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
            $scope.$on( 'quotes.need_update', function( event, project ) {
                Quote.getFromServerByProject(project);
            });
            $scope.$on( 'quote_form.clear', function( event ) {
                $scope.quotes = {};
                $('#quote_view textarea, #quote_view input').val('');
            });
            $scope.$on( 'project.update', function( event, args ) {
                console.log('---->project.update');
                $scope.project = args;
                if(!$scope.project.hasOwnProperty('id') && $scope.quotes.length){
                    Quote.clear();
                }
                console.log('---->project.update');
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

            $scope.$on( 'participants.update', function( event ) {
                $scope.participants = Participant.participants;

                $scope.User = User;
                User.getFromServer('users.update','getAllUsers');

            });
            $scope.$on( 'users.update', function( event ) {
                $scope.users = User.users;
            });
            //$scope.$on( 'clients.update', function( event ) {
            //    $scope.clients = Client.clients;
            //});
            $rootScope.showSystemMsg = function(msg) {
                $("#msg").html(msg);
                $("#msg").show();
                $rootScope. hideTag("#msg", 3000);
            };
            $rootScope.hideTag = function(tag_id,time) {
                setTimeout(function(){
                    $(tag_id).hide();
                }, time);
            };
        }
    ]
);