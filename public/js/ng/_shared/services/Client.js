/**
 * Created by vadym on 7/7/14.
 */

'use strict';

app_module.service( 'Client', [ '$rootScope','$http','API', function( $rootScope, $http, API ) {
    var current_index = null;
    var service = {
        clients: [],


        save: function ( client ) {

            if(!API.validateForm(client, ['name','email'], 'client_')) return false;

            if (typeof client.id === 'undefined' ) {
                service.clients.push( angular.copy( client)  );
            } else {
                // send new data to the server
            }

            this.saveOnServer(client);
            $rootScope.$broadcast('client.update', {});
            $rootScope.$broadcast( 'clients.update' );
            $rootScope.$broadcast('form.to_regular_place');

            this.resetBackupClient();
        },
        remove: function(index) {
            API.removeOne(
                'client',
                null,
                {'id' : service.clients[index].id},
                function(obj) {
                    if (obj.result === 'success') {
//                    console.log(data);
                    } else {
//                    console.log(data);
//                    console.log(status);
                    }
                }
            );
            service.clients.splice(index, 1);
            $rootScope.$broadcast( 'clients.update' );
        },
        delete: function(id) {
            API.removeOne(
                'client',
                null,
                {'id' : id},
                function(obj) {
                    if (obj.result === 'success') {
                        $rootScope.$broadcast('client.update', {});
                        $rootScope.$broadcast('form.to_regular_place');
                        $rootScope.$broadcast( 'clients.need_update' );
                    } else {
                    }
                }
            );
        },
        edit: function(index) {
            console.log('------> edit');
            this.backupClient(index);
            $rootScope.$broadcast('client.update', service.clients[index]);
            $rootScope.$broadcast('form.to_fixed_position',service.clients[index]);
            //$rootScope.$broadcast( 'requirements.update' );
        },
        cancel: function() {
            console.log('------> cancel');
            this.restoreClient();
            this.resetBackupClient();
            $rootScope.$broadcast('client.update', {});
            $rootScope.$broadcast( 'clients.update' );
            $rootScope.$broadcast('form.to_regular_place');
        },
        saveOnServer: function(client) {
            API.saveOne(
                'client',
                null,
                {id : client.id},
                angular.toJson(client),
                function(obj) {
                    if (obj.result === 'success') {
                        $rootScope.$broadcast('clients.need_update' );
//                    console.log(data);
                    } else {
//                    console.log(data);
//                    console.log(status);
                    }
                }
            );
        },
        getFromServer: function() {
            API.getAll(
                'client',
                'getForClient',
                undefined,
                function(obj) {
                    service.clients = obj.data;
                    $rootScope.$broadcast( 'clients.update' );
                }
            );
        },
        showForm: function(index) {
            console.log('------> Show');
            $rootScope.$broadcast('form.to_fixed_position',service.clients[index]);
        },
        backupClient: function(index) {
            current_index = index;
            service.clients[index].backup = angular.copy( service.clients[index]);
//            console.log(service.clients[current_index].backup);
        },
        resetBackupClient: function() {
            if (current_index) {
                service.clients[current_index].backup = {};
                current_index = null;
            }
        },
        restoreClient: function() {
            if (
                typeof service.clients[current_index] !== 'undefined' &&
                typeof service.clients[current_index].backup !== 'undefined'
            ) {
                service.clients[current_index] = service.clients[current_index].backup;
            }
        }
    }

  return service;
}]);