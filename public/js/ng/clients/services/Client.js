/**
 * Created by vadym on 7/7/14.
 */

'use strict';

app_module.service( 'Client', [ '$rootScope','$http', function( $rootScope, $http ) {
    var current_index = null;
    var service = {
        clients: [],

        save: function ( client ) {

            console.log(client);

            if (typeof client.id === 'undefined' ) {
                service.clients.push( jQuery.extend({}, client)  );
            } else {
                // send new data to the server
            }

            this.saveOnServer();
            $rootScope.$broadcast('client.update', {});
            $rootScope.$broadcast( 'clients.update' );
            $rootScope.$broadcast('form.to_regular_place');

            this.resetBackupClient();
        },
        remove: function(index) {
            service.clients.splice(index, 1);
            this.saveOnServer();
            $rootScope.$broadcast( 'clients.update' );
        },
        edit: function(index) {
            console.log('------> edit');
            this.backupClient(index);
            $rootScope.$broadcast('client.update', service.clients[index]);
            $rootScope.$broadcast('form.to_fixed_position',service.clients[index]);
            //$rootScope.$broadcast( 'requirements.update' );
        },
        cancel: function() {
            console.log('------> cencel');
            this.restoreClient();
            this.resetBackupClient();
            $rootScope.$broadcast('client.update', {});
            $rootScope.$broadcast( 'clients.update' );
            $rootScope.$broadcast('form.to_regular_place');
        },
        saveOnServer: function() {
            var url = this.prepareUrl('saveAll',{quote_id: app_module.quote_id});//TODO
            $http.post(url,angular.toJson(service.clients))
                .success(function(data) {
                    console.log(data);
                })
                .error(function(data, status) {
                    console.log(data);
                    console.log(status);
                })
            ;
        },
        getFromServer: function() {
            var url = this.prepareUrl('getForClient',{});
            $http.get(url)
                .success(function(data) {
                    try {
                        var obj = angular.fromJson(data);
                    } catch (e) {
                        alert('Error! No data received.');
                    }
                    if (obj.result === 'success') {
                        service.clients = obj.data;
                        $rootScope.$broadcast( 'clients.update' );
                    } else {
                        alert('Error! No success message received.');
                    }
                })
                .error(function(data, status) {
                    console.log('Error: -------------------->');
                    console.log(data);
                    console.log(status);
                    alert('Error! No data received.');
                })
            ;
        },
        prepareUrl: function(action,args) {
            var url = app_module.base_url + app_module.prefix  + 'api/client/' + action + app_module.postfix;
            if (url.indexOf('?') === false) {
                url = url + '?';
            } else {
                url = url + '&';
            }
            var count = 1;
            $.each(args,function(key,value) {
                if (count > 1) {
                    url = url + '&';
                }
                url = url + key + '=' + value;
                count++;
            });
            return url;
        },
        backupClient: function(index) {
            current_index = index;
            service.clients[index].backup = jQuery.extend({}, service.clients[index]);
            console.log(service.clients[current_index].backup);
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