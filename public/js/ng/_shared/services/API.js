/**
 * Created by vadym on 7/7/14.
 */

'use strict';

app_module.service( 'API', [ '$rootScope','$http', function( $rootScope, $http ) {
    var replace_tag = '<$api_command$>';
    var api_base_url = app_module.api_base_url + 'v1/' + replace_tag + app_module.postfix;
//    var api_base_url = app_module.base_url + app_module.prefix  + 'api/' + replace_tag + app_module.postfix;
    var service = {

        /**
         * @param what     - api page (*required)
         * @param method   - api method (can be null or undefined)
         * @param get_args - Object for GET args (can be null or undefined)
         * @param callback - callback function to be executed on success API request  (can be null or undefined)
         */
        getAll: function(what,method,get_args,callback) {
            if (typeof method === 'undefined' || method === null) {
                method = 'getByField';
            }
            if (typeof get_args === 'undefined' || get_args === null) {
                get_args = {};
            }
            if (typeof callback === 'undefined' || callback === null) {
                callback = function(obj) {};
            }
            var url = this.prepareUrl(what,method,get_args);
            this.request(url,'get',null,callback);
        },

        /**
         * @param what      - api page (*required)
         * @param method    - api method (can be null or undefined)
         * @param get_args  - Object for GET args (can be null or undefined)
         * @param post_data - POST data to be sent to the server (can be null or undefined)
         * @param callback  - callback function to be executed on success API request  (can be null or undefined)
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

        /**
         * @param what      - api page (*required)
         * @param method    - api method (can be null or undefined)
         * @param get_args  - Object for GET args (can be null or undefined)
         * @param callback  - callback function to be executed on success API request  (can be null or undefined)
         */
        getOne: function(what,method,get_args,callback) {
            if (typeof method === 'undefined' || method === null) {
                method = 'getById';
            }
            if (typeof get_args === 'undefined' || get_args === null) {
                get_args = {};
            }
            if (typeof callback === 'undefined' || callback === null) {
                callback = function(obj) {};
            }
            var url = this.prepareUrl(what,method,get_args);
            this.request(url,'get',null,callback);
        },

        /**
         * @param what      - api page (*required)
         * @param method    - api method (can be null or undefined)
         * @param get_args  - Object for GET args (can be null or undefined)
         * @param callback  - callback function to be executed on success API request  (can be null or undefined)
         */
        removeOne: function(what,method,get_args,callback) {
            if (typeof method === 'undefined' || method === null) {
                method = 'deleteById';
            }
            if (typeof get_args === 'undefined' || get_args === null) {
                get_args = {};
            }
            if (typeof callback === 'undefined' || callback === null) {
                callback = function(obj) {};
            }
            var url = this.prepareUrl(what,method,get_args);
            this.request(url,'get',null,callback);
        },

        /**
         * @param url      - url to call
         * @param type     - get | post
         * @param args     - Object for GET args (can be null or undefined)
         * @param callback - callback function to be executed on success API request  (can be null or undefined)
         */
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
                        callback(obj);
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
        },

        /**
         * @param table      - API page
         * @param action     - API action
         * @param args       - Object for GET args (can be null or undefined)
         * @returns {string}
         */
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
//                if (count > 1) {
//                    url = url + '&';
//                }
                url = url + key + '=' + value + '&';
                count++;
            });
            // Token from cookie
            url = url + 'lhash=' + app_module.lhash;
            return url;
        },
        /**
         * Form validation
         * @param obj object
         * @param field string/array
         * @param prefix string - owner's prefix of field's ID
         * @param message string    // default 'required'
         * @param time int - duration of showing message, in seconds    //default 3000
         * @returns {boolean} false if validation failed
         */
        validateForm: function(obj,field,prefix,message,time){
            var passed = true;
            $(".validation_error").remove();
            if (!angular.isDefined(obj)){
                service.addFieldMessage(field,prefix,message,time);
                passed = false;
            } else {
                if(angular.isArray(field)){
                    for(var key in field){
                        var el = field[key];

                        if(!obj[el]){
                            service.addFieldMessage(el,prefix,message,time);
                            passed = false;
                        }else if(el.indexOf('email')>-1){
                            if(!service.validateEmail(obj[el])){
                                service.addFieldMessage(el,prefix,'incorrect e-mail',time);
                                passed = false;
                            }
                        }
                    }
                }else{
                    if(!obj.field){
                        service.addFieldMessage(field,prefix,message,time);
                        passed = false;
                    }else if(field.indexOf('email')>-1){
                        if(!service.validateEmail(obj[field])){
                            service.addFieldMessage(field,prefix,'incorrect e-mail',time);
                            passed = false;
                        }
                    }
                }
            }
            return passed;
        },
        addFieldMessage: function(field, prefix, message, time){

            //defaults
            if(!angular.isDefined(message)) {
                message = 'required';
            }
            if(!prefix){
                prefix='';
            }
            if(!angular.isDefined(time)) {
                time = 10000;
            }

            if(angular.isArray(field)){
                for(var key in field){
                    var el = field[key];
                    if(angular.isString(el) && (el)){
                        $( "#"+prefix+el).parent().after( '<span id="val_error_'+prefix+el+'" class="validation_error">'+message+'<br /></span>' );
                        service.removeTag("#val_error_"+prefix+el,time);
                    }
                }
            }else if(angular.isString(field) && (field)){
                $( "#"+prefix+field).parent().after( '<span id="val_error_'+prefix+field+'" class="validation_error">'+message+'<br /></span>' );
                service.removeTag("#val_error_"+prefix+field,time);
            }
        },

        removeTag: function(tag_id,time) {
            setTimeout(function(){
                $(tag_id).remove();
            }, time);
        },
        validateEmail: function(email) {
            if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)){
                return (true);
            }
            return (false);
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