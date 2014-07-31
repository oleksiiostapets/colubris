/**
 * Created by alf on 7/25/14.
 */

'use strict';

app_module.service( 'Project', [ '$rootScope','$http','API', function( $rootScope, $http, API ) {
    var current_index = null;
    var service = {
        projects: [],

        getFromServer: function(field,value) {

            var field_val = field ? {field:field, value:value} : {};

            API.getAll(
                'project',
                undefined,
                field_val,
                function(obj) {
                    service.projects = obj.data;
                    $rootScope.$broadcast( 'projects.update' );
                }
            );
        },
        remove: function(index) {
            try {
                API.removeOne(
                    'project',
                    'deleteById',
                    {id:service.projects[index].id},
                    function(obj) {
                        if (obj.result === 'success') {
                            service.projects.splice(index, 1);
                            $rootScope.$broadcast( 'projects.update' );
                        } else {
                            alert('Error! No success message received.');
                        }
                    }
                );
            } catch (e) {
                alert('Error! No data received.');
            }
        }
    }
    return service;
}]);