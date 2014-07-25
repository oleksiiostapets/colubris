/**
 * Created by vadym on 7/7/14.
 */

'use strict';

app_module.service( 'Task', [ '$rootScope','$http', function( $rootScope, $http ) {
    var service = {
        tasks: [],
        total_rows: 0,

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
            var url = this.prepareUrl('getByFields',params);
            $http.get(url)
                .success(function(data) {
                    try {
                        var obj = angular.fromJson(data);
                    } catch (e) {
                        alert('Error! No data received.');
                    }
                    if (obj.result === 'success') {
                        service.tasks = obj.data;
                        service.total_rows = obj.total_rows;
                        $rootScope.$broadcast( 'tasks.update' );
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
        paginate: function(page){
            $rootScope.current_page = page;
            this.getFromServer();
        },
        getFromServer: function() {
            var url = this.prepareUrl('getByField',{"count":$rootScope.tasks_on_page,"offset":(($rootScope.current_page-1)*$rootScope.tasks_on_page)});
            $http.get(url)
                .success(function(data) {
                    try {
                        var obj = angular.fromJson(data);
                    } catch (e) {
                        alert('Error! No data received.');
                    }
                    if (obj.result === 'success') {
                        service.tasks = obj.data;
                        service.total_rows = obj.total_rows;
                        $rootScope.$broadcast( 'tasks.update' );
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
            var url = app_module.base_url + app_module.prefix  + 'api/task/' + action + app_module.postfix;
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