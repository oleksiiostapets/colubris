/**
 * Created by alf on 7/25/14.
 */

'use strict';

app_module.service( 'Quote', [ '$rootScope','$http', function( $rootScope, $http ) {
    var current_index = null;
    var service = {
        quotes: [],

        getFromServerByProject: function(project) {
            // Clearing previous quote and requirement parameters to exclude it from crud results
            var params = {};
            $.each($rootScope.filter_values,function(key,value) {
                if (key != 'quote_id' && key != 'requirement_id') params[key]=value;
            });
            $rootScope.filter_values = params;

            var url = this.prepareUrl('getByField',{field:'project_id',value:project.id});
            $http.get(url)
                .success(function(data) {
                    try {
                        var obj = angular.fromJson(data);
                    } catch (e) {
                        alert('Error! No data received.');
                    }
                    if (obj.result === 'success') {
                        service.quotes = obj.data;
                        service.quotes.unshift({id:"",name:'all'})
                        $rootScope.$broadcast( 'quotes.update' );
                    } else {
                        alert('Error! No success message received.');
                    }
                })
                .error(function(data, status) {
                    console.log('Error: -------------------->');
                    console.log(data);
                    console.log(status);
                    alert('Error! No data received.');
                })
            ;
        },
        prepareUrl: function(action,args) {
            var url = app_module.base_url + app_module.prefix  + 'api/quote/' + action + app_module.postfix;
            if (url.indexOf('?') === false) {
                url = url + '?';
            } else {
                url = url + '&';
            }
            var count = 1;
            $.each(args,function(key,value) {
                if (count > 1) {
                    url = url + '&';
                }
                url = url + key + '=' + value;
                count++;
            });
            return url;
        }
    }

    return service;
}]);