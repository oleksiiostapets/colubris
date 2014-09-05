/**
 * Created by alf on 7/25/14.
 */

'use strict';

app_module.service( 'Quote', [ '$rootScope','$http','API', function( $rootScope, $http, API ) {
    var current_index = null;
    var service = {
        quotes: [],

        clear: function(){
            service.quotes = [];
            $rootScope.$broadcast( 'quotes.update' );
        },
        getFromServerByProject: function(project) {
            // Clearing previous quote and requirement parameters to exclude it from crud results
            var params = {};
            if(!project)return;
            $.each($rootScope.filter_values,function(key,value) {
                if (key != 'quote_id' && key != 'requirement_id') params[key]=value;
            });
            $rootScope.filter_values = params;

            API.getAll(
                'quote',
                undefined,
                {field:'project_id',value:project.id},
                function(obj) {
                    service.quotes = obj.data;
                    $rootScope.$broadcast( 'quotes.update' );
                }
            );
        },
        getOneFromServer: function(quote_id){
            var field_val = (quote_id)? {id: quote_id} : {};
            API.getOne(
                'quote',
                undefined,
                field_val,
                function(obj) {
                    service.quotes = obj.data;
                    $rootScope.$broadcast( 'quote.update' );
                }
            );
        }
    }
    return service;
}]);