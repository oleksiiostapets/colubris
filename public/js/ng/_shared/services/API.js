/**
 * Created by vadym on 7/7/14.
 */

'use strict';

app_module.service( 'API', [ '$rootScope','$http', function( $rootScope, $http ) {
    var replace_tag = '<$api_command$>';
    var api_base_url = app_module.base_url + app_module.prefix  + 'api/' + replace_tag + app_module.postfix;
    var service = {

        getAll: function(what) {

        },
        getOne: function(what,one) {

        },
        updateOne: function(what,one) {

        },
        saveOne: function(what,one,method) {
            if (typeof method === 'undefined') {
                method = 'saveParams';
            }
            var url = this.prepareUrl('comment',method,{id: one.id});

            console.log('### prepared Url');
            console.log(url);
            return;
        },
        removeOne: function(what,one) {

        },

        prepareUrl: function(table,action,args) {
            var url = api_base_url;
            var api_command = table + '/' + action;
            url = url.replace(replace_tag,api_command);

            //var url = app_module.base_url + app_module.prefix  + 'api/reqcomment/' + action + app_module.postfix;
            if (url.indexOf('?') === false) {
                url = url + '?';
            } else {
                url = url + '&';
            }
            var count = 1;
            $.each(args,function(key,value) {
                if (typeof value === 'undefined') {
                    return;
                }
                if (count > 1) {
                    url = url + '&';
                }
                url = url + key + '=' + value;
                count++;
            });
            return url;
        },

//////////////////////
        saveOnServer: function(comm) {
            var url = this.prepareUrl('saveParams',{id: comm.id});
            $http.post(url,comm)
                .success(function(data) {
                    console.log(data);
                })
                .error(function(data, status) {
                    console.log(data);
                    console.log(status);
                })
            ;
        },
        getFromServer: function(requirement_id) {
            var url = this.prepareUrl('getByField',{field:'requirement_id',value: requirement_id});
            $http.get(url)
                .success(function(data) {
                    try {
                        var obj = angular.fromJson(data);
                    } catch (e) {
                        alert('Error! No data received.');
                    }
                    if (obj.result === 'success') {
                        service.comments = obj.data;
                        $rootScope.$broadcast( 'comments.update' );
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
        }
    }

  return service;
}]);