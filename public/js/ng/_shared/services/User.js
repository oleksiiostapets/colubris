/**
 * Created by alf on 7/25/14.
 */

'use strict';

app_module.service( 'User', [ '$rootScope','$http','API', function( $rootScope, $http, API ) {
    var current_index = null;
    var service = {
        users: [],

        getFromServer: function(broadcast_message) {


            API.getAll(
                'user',
                'getUsers',
                undefined,
                function(obj) {
                    service.users = obj.data;
                    $rootScope.$broadcast( broadcast_message );
                }
            );
        },
        getFromServerByProject: function(project) {
            if(!project)return;
            API.getAll(
                'user',
                'getUsersByProject',
                {project_id:project.id},
                function(obj) {
                    service.users = obj.data;
                    $rootScope.$broadcast( 'assigneds.update' );
                }
            );
        },
        save: function ( user ) {

            if(this.validateForm(user)==true){
                if (typeof user.id === 'undefined' ) {
                    service.users.push( jQuery.extend({}, user)  );
                }
                this.saveOnServer(user);

                $rootScope.$broadcast('user.update', {});
                $rootScope.$broadcast('users.update' );
                $rootScope.$broadcast('form.to_regular_place');
                this.resetBackupUser();
            }
        },
        validateForm: function(user){
            $(".validation_error").remove();
            if (typeof user === 'undefined'){
                $( "#user_name").parent().after( '<span id="val_error_user_name" class="validation_error">required<br /></span>' );
                $rootScope.removeTag("#val_error_user_name", 3000);
                $( "#user_email").parent().after( '<span id="val_error_user_email" class="validation_error">required<br /></span>' );
                $rootScope.removeTag("#val_error_user_email", 3000);
                return false;
            } else {
                if (typeof user.name === 'undefined' || user.name == ''){
                    $( "#user_name").parent().after( '<span id="val_error_user_name" class="validation_error">required<br /></span>' );
                    $rootScope.removeTag("#val_error_user_name", 3000);
                    return false;
                }
                if (typeof user.email === 'undefined' || user.email == ''){
                    $( "#user_email").parent().after( '<span id="val_error_user_email" class="validation_error">required<br /></span>' );
                    $rootScope.removeTag("#val_error_user_email", 3000);
                    return false;
                }
                if (!this.validatecontactEmail(user.email)){
                    $( "#user_email").parent().after( '<span id="val_error_user_email" class="validation_error">wrong email<br /></span>' );
                    $rootScope.removeTag("#val_error_user_email", 3000);
                    return false;
                }
            }
            return true;
        },
        saveOnServer: function(user) {
            API.saveOne(
                'user',
                'saveUser',
                {id : user.id, name : user.name, email : user.email},
                angular.toJson(service.users),
                function(obj) {
                    if (obj.result === 'success') {
                        $rootScope.$broadcast('users.need_update' );
                        alert('Saved.');
                    } else if (obj.result === 'validation_error') {
                        $( "#user_email").parent().after( '<span id="val_error_user_email" class="validation_error">' + obj.message + '<br /></span>' );
                        $rootScope.removeTag("#val_error_user_email", 3000);
                    } else {
                        alert('Error! No success message received.');
                    }
                }
            );
            this.getFromServer('users.update');
        },
        remove: function(index) {
            API.removeOne(
                'user',
                null,
                {'id' : service.users[index].id},
                function(obj) {
                    if (obj.result === 'success') {
//                    console.log(data);
                    } else {
//                    console.log(data);
//                    console.log(status);
                    }
                }
            );
            service.users.splice(index, 1);
            $rootScope.$broadcast( 'users.update' );
        },
        delete: function(id) {
            API.removeOne(
                'user',
                null,
                {'id' : id},
                function(obj) {
                    if (obj.result === 'success') {
                        $rootScope.$broadcast('user.update', {});
                        $rootScope.$broadcast('form.to_regular_place');
                        $rootScope.$broadcast( 'users.need_update' );
                    } else {
                    }
                }
            );
        },
        edit: function(index) {
            console.log('------> edit');
            this.backupUser(index);
            $rootScope.$broadcast('user.update', service.users[index]);
            $rootScope.$broadcast('form.to_fixed_position',service.users[index]);
        },
        backupUser: function(index) {
            current_index = index;
            service.users[index].backup = jQuery.extend({}, service.users[index]);
        },
        cancel: function() {
            this.restoreUser();
            this.resetBackupUser();
            $rootScope.$broadcast('user.update', {});
            $rootScope.$broadcast( 'users.update' );
            $rootScope.$broadcast('form.to_regular_place');
        },
        resetBackupUser: function() {
            if (current_index) {
                service.users[current_index].backup = {};
                current_index = null;
            }
        },
        restoreUser: function() {
            if (
                typeof service.users[current_index] !== 'undefined' &&
                    typeof service.users[current_index].backup !== 'undefined'
                ) {
                service.users[current_index] = service.users[current_index].backup;
            }
        },
        validatecontactEmail: function(email) {
            if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)){
                return (true);
            }
            return (false);
        }
    }

    return service;
}]);