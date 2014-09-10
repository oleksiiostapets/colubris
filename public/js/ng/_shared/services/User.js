/**
 * Created by alf on 7/25/14.
 */

'use strict';

app_module.service( 'User', [ '$rootScope','$http','API', function( $rootScope, $http, API ) {
    var current_index = null;
    var service = {
        users: [],

        getFromServer: function(broadcast_message) {


            API.getAll(
                'user',
                'getUsers',
                undefined,
                function(obj) {
                    service.users = obj.data;
                    $rootScope.$broadcast( broadcast_message );
                }
            );
        },
        getFromServerByProject: function(project) {
            if(!project)return;
            API.getAll(
                'user',
                'getUsersByProject',
                {project_id:project.id},
                function(obj) {
                    service.users = obj.data;
                    $rootScope.$broadcast( 'assigneds.update' );
                }
            );
        },
        save: function ( user ) {

            if (typeof user.id === 'undefined' ) {
                service.users.push( jQuery.extend({}, user)  );
            } else {
                // send new data to the server
            }
            this.saveOnServer(user);
            $rootScope.$broadcast('user.update', {});
            $rootScope.$broadcast('users.update' );
            $rootScope.$broadcast('form.to_regular_place');

            this.resetBackupUser();
        },
        saveOnServer: function(user) {
            API.saveOne(
                'user',
                null,
                {id : user.id, name : user.name, email : user.email},
                angular.toJson(service.users),
                function(obj) {
                    if (obj.result === 'success') {
                        alert('Saved.');
                    } else {
                        alert('Error! No success message received.');
                    }
                }
            );
        },
        remove: function(index) {
            service.users.splice(index, 1);
            this.saveOnServer();
            $rootScope.$broadcast( 'users.update' );
        },
        edit: function(index) {
            console.log('------> edit');
            this.backupUser(index);
            $rootScope.$broadcast('user.update', service.users[index]);
            $rootScope.$broadcast('form.to_fixed_position',service.users[index]);
        },
        backupUser: function(index) {
            current_index = index;
            service.users[index].backup = jQuery.extend({}, service.users[index]);
        },
        cancel: function() {
            this.restoreUser();
            this.resetBackupUser();
            $rootScope.$broadcast('user.update', {});
            $rootScope.$broadcast( 'users.update' );
            $rootScope.$broadcast('form.to_regular_place');
        },
        resetBackupUser: function() {
            if (current_index) {
                service.users[current_index].backup = {};
                current_index = null;
            }
        },
        restoreUser: function() {
            if (
                typeof service.users[current_index] !== 'undefined' &&
                    typeof service.users[current_index].backup !== 'undefined'
                ) {
                service.users[current_index] = service.users[current_index].backup;
            }
        }
    }

    return service;
}]);