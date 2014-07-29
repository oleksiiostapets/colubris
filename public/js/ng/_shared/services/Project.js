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
                    if(!field)   service.projects.unshift({id:"",name:'all'});  //default element for filter by field
                    $rootScope.$broadcast( 'projects.update' );
                }
            );
        }
    }
    return service;
}]);