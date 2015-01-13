/**
 * Created by vadym on 7/7/14.
 */

'use strict';

app_module.service( 'Task', [ '$rootScope','$http', 'API', function( $rootScope, $http, API ) {
    var current_index = null;
    var service = {
        tasks: [],
        task_statuses: [],
        total_rows: 0,

        getStatusesFromServer: function() {

            API.getAll(
                'task',
                'getStatuses',
                undefined,
                function(obj) {
                    service.task_statuses = obj.data;
                    service.task_statuses.unshift({id:"",name:'all'})
                    $rootScope.$broadcast( 'task_statuses.update' );
                }
            );
        },
        filter: function(field,value){
            //console.log(value);
            $rootScope.filter_values[field] = value.id;
            $rootScope.current_page = 1;
            this.getFromServerByFields();
        },
        getFromServerByFields: function() {
            var params = {"count":$rootScope.tasks_on_page,"offset":(($rootScope.current_page-1)*$rootScope.tasks_on_page)};
            var count = 1;
            $.each($rootScope.filter_values,function(key,value) {
                params['field'+count]=key;
                params['value'+count]=value;
                count++;
            });

            API.getAll(
                'task',
                'getByFields',
                params,
                function(obj) {
                    service.tasks = obj.data;
                    service.total_rows = obj.total_rows;
                    $rootScope.$broadcast( 'tasks.update' );
                }
            );
        },
        getFromServerByReqvId: function(reqv_id) {
            var params = {"field":"requirement_id","value":reqv_id};

            API.getAll(
                'task',
                'getByField',
                params,
                function(obj) {
                    $.each(obj.data,function(key,value) {
                        if(app_module.current_user_rights.indexOf('can_delete_task') != -1){
                            obj.data[key]['can_delete_task'] = 'display: block;';
                        }
                    });

                    service.tasks = obj.data;
                    service.total_rows = obj.total_rows;
                    $rootScope.$broadcast( 'reqv_tasks.update',service.tasks );
                }
            );
        },
        paginate: function(page){
            $rootScope.current_page = page;
            this.getFromServerByFields();
        },
        getFromServer: function() {
            API.getAll(
                'task',
                'getByField',
                {"count":$rootScope.tasks_on_page,"offset":(($rootScope.current_page-1)*$rootScope.tasks_on_page)},
                function(obj) {
                    service.tasks = obj.data;
                    service.total_rows = obj.total_rows;
                    $rootScope.$broadcast( 'tasks.update' );
                }
            );
        },

        save: function ( task ) {

            //if(!API.validateForm(task, ['name','client'], 'project_')) return false;

            if (typeof task.id === 'undefined' ) {
                service.tasks.push( angular.copy(task));
            } else {
                // refresh crud data on the client
                if(current_index){
                    service.tasks[current_index]=task;
                }
                // send new data to the server
            }
            this.saveOnServer(task);
            this.resetbackupTask();
            $rootScope.$broadcast('task.update', {});
            $rootScope.$broadcast('task.clear');
        },

        saveOnServer: function(task) {
            API.saveOne(
                'task',
                null,
                {id : task.id},
                angular.toJson(task),
                function(obj) {
                    $rootScope.$broadcast('tasks.need_update' );
                    //$rootScope.$broadcast('form.to_regular_place');
                }
            );
        },

        edit: function(index) {
            console.log('------> Task edit');
            this.backupTask(index);
            $rootScope.$broadcast('task.update', service.tasks[index]);
            $rootScope.$broadcast('form.to_fixed_position',service.tasks[index]);
        },

        remove: function(index) {
            try {
                API.removeOne(
                    'task',
                    'deleteById',
                    {id:service.tasks[index].id},
                    function(obj) {
                        if (obj.result === 'success') {
                            service.tasks.splice(index, 1);
                            $rootScope.$broadcast( 'tasks.update' );
                        } else {
                            alert('Error! No success message received.');
                        }
                    }
                );
            } catch (e) {
                alert('Error! No data received.');
            }
        },

        delete: function(id) {
            API.removeOne(
                'task',
                null,
                {'id' : id},
                function(obj) {
                    if (obj.result === 'success') {
                        $rootScope.$broadcast('task.update', {});
                        //$rootScope.$broadcast('form.to_regular_place');
                        $rootScope.$broadcast( 'tasks.update' );
                        $rootScope.$broadcast( 'tasks.need_update' );
                    } else {
                    }
                }
            );
        },

        cancel: function() {
            console.log('------> cancel');
            this.restoreTask();
            this.resetbackupTask();
            $rootScope.$broadcast('task.update', {});
            $rootScope.$broadcast('tasks.update' );
            $rootScope.$broadcast('time.clear');
            $rootScope.$broadcast('form.to_regular_place');
        },

        showForm: function(index) {
            console.log('------> Show');
            $rootScope.$broadcast('form.to_fixed_position',service.tasks[index]);
        },

        backupTask: function(index) {
            current_index = index;
            service.tasks[index].backup = angular.copy( service.tasks[index]);
            //console.log(service.tasks[current_index].backup);
        },

        resetbackupTask: function() {
            if (current_index) {
                service.tasks[current_index].backup = {};
                current_index = null;
            }
        },

        restoreTask: function() {
            if (
                current_index &&
                angular.isDefined(service.tasks[current_index]) &&
                angular.isDefined(service.tasks[current_index].backup)
            ) {
                service.tasks[current_index] = service.tasks[current_index].backup;
            }
        }
    };

  return service;
}]);