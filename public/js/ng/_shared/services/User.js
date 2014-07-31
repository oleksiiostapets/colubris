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
                undefined,
                undefined,
                function(obj) {
                    service.users = obj.data;
                    $rootScope.$broadcast( broadcast_message );
                }
            );
        }
    }

    return service;
}]);