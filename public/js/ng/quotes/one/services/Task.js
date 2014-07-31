/**
 * Created by vadym on 7/7/14.
 */

'use strict';

app_module.service( 'Task', [ '$rootScope','$http', function( $rootScope, $http ) {
    var current_index = null;
    var service = {
        tasks: [],

        save: function ( task ) {

            console.log(task);

            if (typeof task.id === 'undefined' ) {
                service.tasks.push( angular.copy( task)  );
            } else {
                // send new data to the server
            }

            this.saveOnServer();
            $rootScope.$broadcast('task.update', {});
            $rootScope.$broadcast( 'tasks.update' );

            this.resetBackupComm();
        },
        remove: function(index) {
            service.tasks.splice(index, 1);
            this.saveOnServer();
            $rootScope.$broadcast( 'tasks.update' );
        },
        edit: function(index) {
            console.log('------> edit');
            this.backupComm(index);
            $rootScope.$broadcast('task.update', service.tasks[index]);
            //$rootScope.$broadcast( 'tasks.update' );
        },
        cancel: function(task) {
            console.log('------> cencel');
            this.restoreComm();
            this.resetBackupComm();
            $rootScope.$broadcast('task.update', {});
            $rootScope.$broadcast( 'tasks.update' );
        },
//        saveOnServer: function() {
//            var url = this.prepareUrl('saveAll',{quote_id: app_module.quote_id});
//            $http.post(url,angular.toJson(service.requirements))
//                .success(function(data) {
//                    console.log(data);
//                })
//                .error(function(data, status) {
//                    console.log(data);
//                    console.log(status);
//                })
//            ;
//        },
        getFromServer: function(requirement_id) {
            var url = this.prepareUrl('getByField',{field:'requirement_id',value: requirement_id});
            $http.get(url)
                .success(function(data) {
                    try {
                        var obj = angular.fromJson(data);
                    } catch (e) {
                        alert('Error! No data received.');
                    }
                    if (obj.result === 'success') {
                        service.tasks = obj.data;
                        $rootScope.$broadcast( 'tasks.update' );
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
            var url = app_module.base_url + app_module.prefix  + 'api/task/' + action + app_module.postfix;
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
        backupComm: function(index) {
            current_index = index;
            service.tasks[index].backup = angular.copy( service.tasks[index]);
        },
        resetBackupComm: function() {
            if (current_index) {
                service.tasks[current_index].backup = {};
                current_index = null;
            }
        },
        restoreComm: function() {
            service.tasks[current_index] = service.tasks[current_index].backup;
        }
    }

  return service;
}]);