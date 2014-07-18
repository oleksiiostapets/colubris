'use strict';

function hideTag(tag_id,time){
    setTimeout(function(){
        $(tag_id).hide();
    }, time);
}
function removeTag(tag_id,time){
    setTimeout(function(){
        $(tag_id).remove();
    }, time);
}
function showSystemMsg(msg){
    $("#msg").html(msg);
    $("#msg").show();
    hideTag("#msg", 3000);
}

app_module.service( 'Settings', [ '$rootScope','$http', function( $rootScope, $http ) {
    var current_index = null;
    var service = {
        settings: [],

        upload: function ( account ) {

            console.log('upload');
            console.log(account);

            //this.saveOnServer(account);
            //$rootScope.$broadcast('account.update', {});
        },
        save: function ( account ) {

            //console.log(account);

            this.saveOnServer(account);
            //$rootScope.$broadcast('account.update', {});
        },
        savePassword: function ( account ) {

            console.log(account);

            this.changePasswordOnServer(account);
            //$rootScope.$broadcast('account.update', {});
        },
        changePasswordOnServer: function(account) {
            //console.log(old_password);
            var url = this.prepareUrl('api/account/','changePassword',{id : account.id, old_password : account.old_password, new_password : account.new_password, verify_password : account.verify_password});
            $http.get(url)
                .success(function(data) {
                    try {
                        var obj = angular.fromJson(data);
                    } catch (e) {
                        alert('Error! No data received.');
                    }
                    //console.log(obj);
                    $(".validation_error").remove();
                    if (obj.result === 'success') {
                        showSystemMsg('password changed');
                        $rootScope.$broadcast( 'settings.password_changed', null );
                    } else if(obj.result === 'validation_error') {
                        var i = 0;
                        $.each(obj.errors,function(key,value) {
                            $.each(value,function(key2,value2) {
                                i = i + 1;
                                $( "#" + key2).parent().after( '<span id="val_error_' + i + '" class="validation_error">' + value2 + '<br /></span>' );
                                removeTag("#val_error_" + i, 3000);
                            });
                        });
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
        saveOnServer: function(account) {
            var url = this.prepareUrl('api/account/','saveParams',{id : account.id, name : account.name, mail_task_changes : account.mail_task_changes});
            $http.get(url)
                .success(function(data) {
                    try {
                        var obj = angular.fromJson(data);
                    } catch (e) {
                        alert('Error! No data received.');
                    }
                    if (obj.result === 'success') {
                        showSystemMsg('saved');
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
        getFromServer: function() {
            var url = this.prepareUrl('api/account/','getById',{id: app_module.user_id});
            $http.get(url)
                .success(function(data) {
                    //console.log(data);
                    try {
                        var obj = angular.fromJson(data);
                    } catch (e) {
                        alert('Error! No data received.');
                    }
                    if (obj.result === 'success') {
                        service.settings = obj.data;
                        $rootScope.$broadcast( 'settings.update',service.settings );
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
        prepareUrl: function(page,action,args) {
            var url = app_module.base_url + app_module.prefix  + page + action + app_module.postfix;
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
        },
        backupReqv: function(index) {
            current_index = index;
            service.settings[index].backup = jQuery.extend({}, service.settings[index]);
            console.log(service.settings[current_index].backup);
        },
        resetBackupReqv: function() {
            if (current_index) {
                service.settings[current_index].backup = {};
                current_index = null;
            }
        },
        restoreReqv: function() {
            if (
                typeof service.settings[current_index] !== 'undefined' &&
                typeof service.settings[current_index].backup !== 'undefined'
            ) {
                service.settings[current_index] = service.settings[current_index].backup;
            }
        }
    }

  return service;
}]);