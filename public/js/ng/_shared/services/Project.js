/**
 * Created by alf on 7/25/14.
 */

'use strict';

app_module.service( 'Project', [ '$rootScope','$http', function( $rootScope, $http ) {
    var current_index = null;
    var service = {
        projects: [],

        getFromServer: function() {


            var url = this.prepareUrl('getByField',{});
            $http.get(url)
                .success(function(data) {
                    try {
                        var obj = angular.fromJson(data);
                    } catch (e) {
                        alert('Error! No data received.');
                    }
                    if (obj.result === 'success') {
                        service.projects = obj.data;
                        service.projects.unshift({id:"",name:'all'})
                        $rootScope.$broadcast( 'projects.update' );
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
            var url = app_module.base_url + app_module.prefix  + 'api/project/' + action + app_module.postfix;
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