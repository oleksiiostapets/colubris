/**
 * Created by vadym on 7/7/14.
 */

'use strict';

app_module.service( 'Task', [ '$rootScope','$http', 'API', function( $rootScope, $http, API ) {
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
        }
    }

  return service;
}]);