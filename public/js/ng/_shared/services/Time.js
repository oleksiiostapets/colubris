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
        }
    };

  return service;
}]);