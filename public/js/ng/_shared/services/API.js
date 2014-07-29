/**
 * Created by vadym on 7/7/14.
 */

'use strict';

app_module.service( 'API', [ '$rootScope','$http', function( $rootScope, $http ) {
    var replace_tag = '<$api_command$>';
    var api_base_url = app_module.base_url + app_module.prefix  + 'api/' + replace_tag + app_module.postfix;
    var service = {

        /**
         * @param what     - api page (*required)
         * @param method   - api method (can be null or undefined)
         * @param args     - Object for GET args (can be null or undefined)
         * @param callback - callback to be performed on success API request  (can be null or undefined)
         */
        getAll: function(what,method,args,callback) {
            if (typeof method === 'undefined' || method === null) {
                method = 'getByField';
            }
            if (typeof args === 'undefined' || args === null) {
                args = {};
            }
            if (typeof callback === 'undefined' || callback === null) {
                callback = function(obj) {};
            }
            var url = this.prepareUrl(what,method,args);
            this.request(url,'get',null,callback);
        },

        /**
         * @param what      - api page (*required)
         * @param method    - api method (can be null or undefined)
         * @param get_args  - Object for GET args (can be null or undefined)
         * @param post_data - POST data to be sent to the server (can be null or undefined)
         * @param callback  - callback to be performed on success API request  (can be null or undefined)
         */
        saveOne: function(what,method,get_args,post_data,callback) {
            if (typeof method === 'undefined' || method === null) {
                method = 'saveParams';
            }
            if (typeof get_args === 'undefined' || get_args === null) {
                get_args = {};
            }
            if (typeof callback === 'undefined' || callback === null) {
                callback = function(obj) {};
            }
            var url = this.prepareUrl(what,method,get_args);
            this.request(url,'post',post_data,callback);
        },
        getOne: function(what,one) {

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
        request: function(url,type,args,callback) {
            if (typeof type === 'undefined' || type === null) {
                type = 'get';
            }
            if (typeof args === 'undefined' || args === null) {
                args = {};
            }

            switch (type) {
                case 'get':
                    var request = $http.get(url);
                    break;
                case 'post':
                    var request = $http.post(url,args);
                    break;
                default:
                    // TODO: Error message to console here
            }

            if (typeof request !== 'undefined') {
                request
                    .success(function(data) {
                        try {
                            var obj = angular.fromJson(data);
                        } catch (e) {
                            alert('Error! Data is not a peropper JSON.');
                        }
                        if (obj.result === 'success') {
                            callback(obj);
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
            } else {
                alert("typeof type === 'undefined'");
            }
        }

//////////////////////
//        saveOnServer: function(comm) {
//            var url = this.prepareUrl('saveParams',{id: comm.id});
//            $http.post(url,comm)
//                .success(function(data) {
//                    console.log(data);
//                })
//                .error(function(data, status) {
//                    console.log(data);
//                    console.log(status);
//                })
//            ;
//        },
//        getFromServer: function(requirement_id) {
//            var url = this.prepareUrl('getByField',{field:'requirement_id',value: requirement_id});
//            $http.get(url)
//                .success(function(data) {
//                    try {
//                        var obj = angular.fromJson(data);
//                    } catch (e) {
//                        alert('Error! No data received.');
//                    }
//                    if (obj.result === 'success') {
//                        service.comments = obj.data;
//                        $rootScope.$broadcast( 'comments.update' );
//                    } else {
//                        alert('Error! No success message received.');
//                    }
//                })
//                .error(function(data, status) {
//                    console.log('Error: -------------------->');
//                    console.log(data);
//                    console.log(status);
//                    alert('Error! No data received.');
//                })
//            ;
//        }
    }

  return service;
}]);