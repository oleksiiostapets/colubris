'use strict';

app_module.service( 'Setting', [ '$rootScope','$http','API', function( $rootScope, $http, API ) {
    var current_index = null;
    var service = {
        settings: [],

        upload: function ( account ) {

            //console.log('upload');
            //console.log(account);

            //this.saveOnServer(account);
            //$rootScope.$broadcast('account.update', {});
        },
        save: function ( account ) {

            //console.log(account);

            this.saveOnServer(account);
            //$rootScope.$broadcast('account.update', {});
        },
        savePassword: function ( account ) {

            //console.log(account);

            this.changePasswordOnServer(account);
            //$rootScope.$broadcast('account.update', {});
        },
        changePasswordOnServer: function(account) {
            //console.log(old_password);
            API.saveOne(
                'account',
                'changePassword',
                {id : account.id, old_password : account.old_password, new_password : account.new_password, verify_password : account.verify_password},
                angular.toJson(service.settings),
                function(obj) {
                    $(".validation_error").remove();
                    console.log(obj[0]);
                    if (obj.result === 'success') {
                        $rootScope.showSystemMsg('password changed');
                        $rootScope.$broadcast( 'settings.password_changed', null );
                    } else if(obj.result === 'validation_error') {
                        var i = 0;
                        $.each(obj.errors,function(key,value) {
                            $.each(value,function(key2,value2) {
                                i = i + 1;
                                $( "#" + key2).parent().after( '<span id="val_error_' + i + '" class="validation_error">' + value2 + '<br /></span>' );
                                $rootScope.removeTag("#val_error_" + i, 3000);
                            });
                        });
                    } else if(obj[0].message != '') {
                        alert(obj[0].message);
                    } else {
                        alert('Error! No success message received.');
                    }
                }
            );
        },
        saveOnServer: function(account) {
            API.saveOne(
                'account',
                null,
                {id : account.id, name : account.name, mail_task_changes : account.mail_task_changes},
                angular.toJson(service.settings),
                function(obj) {
                    if (obj.result === 'success') {
                        $rootScope.showSystemMsg('saved');
                    } else {
                        alert('Error! No success message received.');
                    }
                }
            );
        },
        getFromServer: function() {
            API.getAll(
                'account',
                'getById',
                {id: app_module.user_id},
                function(obj) {
                    service.settings = obj.data;
                    $rootScope.$broadcast( 'settings.update',service.settings );
                }
            );
        }
    }

  return service;
}]);