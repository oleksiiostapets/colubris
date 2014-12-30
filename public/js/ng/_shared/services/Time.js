/**
 * Created by vadym on 7/7/14.
 */

'use strict';

app_module.service( 'Time', [ '$rootScope','$http', 'API', function( $rootScope, $http, API ) {
    var current_index = null;
    var service = {
        Times: [],

        getFromServerByTask: function(task) {
            API.getAll(
                'time',
                undefined,
                {field:'task_id',value:task.id},
                function(obj) {
                    $.each(obj.data,function(key,value) {
                        if(app_module.current_user_rights.indexOf('can_track_time') == -1){
                            obj.data[key]['allow_del_css'] = 'display: none;';
                        }
                        if(value.user_id != app_module.user_id) {
                            obj.data[key]['allow_del_css'] = 'display: none;';
                        }
                    });
                    service.times = obj.data;
                    //console.log(obj.data);
                    $rootScope.$broadcast( 'times.update', task );
                }
            );
        },
        clear: function(){
            service.times = [];
            $rootScope.$broadcast( 'times.update' );
        },
        save: function ( time, task ) {
            this.saveOnServer(time,task);
        },
        remove: function(id,task) {
            try {
                API.removeOne(
                    'time',
                    'deleteById',
                    {id:id},
                    function(obj) {
                        if (obj.result === 'success') {
                            $rootScope.$broadcast( 'times.update');
                            $rootScope.$broadcast('times.need_update', task );
                        } else {
                            console.log(obj);
                            alert('Error! No success message received.');
                        }
                    }
                );
            } catch (e) {
                console.log(e);
                alert('Error! No data received.');
            }
        },
        saveOnServer: function(time,task) {
            time.task_id = task.id;
            time.user_id = app_module.user_id;
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