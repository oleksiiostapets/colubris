/**
 * Created by vadym on 7/7/14.
 */

'use strict';

app_module.service( 'Time', [ '$rootScope','$http', 'API', function( $rootScope, $http, API ) {
    var current_index = null;
    var service = {
        Times: [],

        getFromServer: function() {
            API.getAll(
                'time',
                'getByField',
                null,
                function(obj) {
                    service.times = obj.data;
                    service.total_rows = obj.total_rows;
                    $rootScope.$broadcast( 'times.update' );
                }
            );
        },
        getFromServerByTask: function(task) {
            API.getAll(
                'time',
                undefined,
                {field:'task_id',value:task.id},
                function(obj) {
                    service.times = obj.data;
                    $rootScope.$broadcast( 'times.update', task );
                }
            );
        },
        save: function ( time, task ) {
            this.saveOnServer(time,task);
        },
        clear: function(){
            service.times = [];
            $rootScope.$broadcast( 'times.update' );
        },
        saveOnServer: function(time,task) {
            API.saveOne(
                'time',
                null,
                {id : time.id, task_id : task.id},
                angular.toJson(time),
                function(obj) {
                    $rootScope.$broadcast('times.need_update', task );
                }
            );
            this.resetBackupTime();
        },
        backupTime: function(index) {
            this.current_index = index;
            service.times[index].backup = angular.copy(service.times[index]);
        },
        resetBackupTime: function() {
            if (this.current_index) {
                service.times[this.current_index].backup = {};
                this.current_index = null;
            }
        },
        restoreTime: function() {
            if (
                typeof service.times[this.current_index] !== 'undefined' &&
                typeof service.times[this.current_index].backup !== 'undefined'
            ) {
                service.times[this.current_index] = service.times[this.current_index].backup;
            }
        }
    };

  return service;
}]);