/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.controller(
    'inlineCrud',
    ['$scope','$document','$http','User','$rootScope', 'Right',
        function ($scope,  $document,  $http,  User,  $rootScope, Right) {

            $rootScope.rows_on_page = 10;
            $rootScope.current_page = 1;
            $rootScope.paginator_max_shift = 10;

            $scope.User = User;
            $scope.users = User.users;
            User.getFromServer('users.update');

            // rights
            $scope.rights = null;
            $scope.Right = Right;
            $scope.rights = Right.rights;

            $scope.$on( 'rights.update', function( event ) {
                $scope.rights = Right.rights;
//                console.log(Right.rights);
            });

            $scope.$on( 'user.update', function( event, args ) {
                $scope.user = args;
            });
            $scope.$on( 'users.need_update', function( event, args ) {
                User.getFromServer('users.update');
            });
            $scope.$on( 'users.update', function( event ) {
                $scope.users = User.users;

                // Paginator
                var total_rows = User.total_rows;
                var total_pages = Math.ceil(total_rows/$rootScope.rows_on_page);
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


            $rootScope.hideTag = function(tag_id,time) {
                setTimeout(function(){
                    $(tag_id).hide();
                }, time);
            }
            $rootScope.removeTag = function(tag_id,time) {
                setTimeout(function(){
                    $(tag_id).remove();
                }, time);
            }
            $rootScope.showSystemMsg = function(msg) {
                $("#msg").html(msg);
                $("#msg").show();
                $rootScope. hideTag("#msg", 3000);
            }
        }]
);