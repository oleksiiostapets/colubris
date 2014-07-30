/**
 * Created by alf on 7/25/14.
 */

'use strict';

app_module.service( 'TaskStatus', [ '$rootScope','$http','API', function( $rootScope, $http, API ) {
    var current_index = null;
    var service = {
        task_statuses: [],

        getFromServer: function() {

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
        }
    }

    return service;
}]);