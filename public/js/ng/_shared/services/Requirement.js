/**
 * Created by alf on 7/25/14.
 */

'use strict';

app_module.service( 'Requirement', [ '$rootScope','$http','API', function( $rootScope, $http, API ) {
    var current_index = null;
    var service = {
        requirements: [],

        clear: function(){
            service.requirements = [];
            $rootScope.$broadcast( 'requirements.update' );
        },
        getFromServerByQuote: function(quote) {
            // Clearing previous requirement parameters to exclude it from crud results
            var params = {};
            $.each($rootScope.filter_values,function(key,value) {
                if (key != 'requirement_id') params[key]=value;
            });
            $rootScope.filter_values = params;

            API.getAll(
                'requirement',
                undefined,
                {field:'quote_id',value:quote.id},
                function(obj) {
                    service.requirements = obj.data;
                    service.requirements.unshift({id:"",name:'all'})
                    $rootScope.$broadcast( 'requirements.update' );
                }
            );
        }
    }
    return service;
}]);