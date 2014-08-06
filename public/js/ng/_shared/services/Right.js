/**
 * Created by alf on 7/25/14.
 */

'use strict';

app_module.service( 'Right', [ '$rootScope','$http','API', function( $rootScope, $http, API ) {
    var current_index = null;
    var service = {
        rights: [],
        all_rights:[],

        getFromServer: function(broadcast_message) {


            API.getAll(
                'right',
                undefined,
                undefined,
                function(obj) {
                    service.rights = obj.data;
                    $rootScope.$broadcast( broadcast_message );
                }
            );
        },
        getAllRights: function(user,broadcast_message) {
            var that = this;
            API.getOne(
                'right',
                'getAvailableRights',
                undefined,
                function(obj) {
                    if (obj.result === 'success') {
                        that.getForUser(user,broadcast_message, obj.data);
                    } else {
                        alert('Error! getAllRights method failed');
                    }
                }
            );
        },
        getForUser: function(user,broadcast_message, all_rights) {
            console.log('-----> getForUser()');
            var that = this;
            API.getOne(
                'right',
                'getByField',
                {field: 'user_id', value:user.id},
                function(obj) {
                    var data;
                    data = that.prepareArray(obj,all_rights);
                    service.rights = data;
                    $rootScope.$broadcast( broadcast_message );
                }
            );
        },
        prepareArray: function(obj,all_rights){
            console.log('-----> prepareArray()');

            var can_rights = obj.data[0].right.split(',');
            var rights_obj = {};
            $.each(all_rights, function(index, value) {
                if(can_rights.indexOf(value) === -1){
                    rights_obj[index] = [value,false];
                }else{
                    rights_obj[index] = [value,true];
                }
            });
//            console.log(rights_obj);
            return rights_obj;
        },
        save: function ( right ) {

            if (typeof right.id === 'undefined' ) {
                service.users.push( jQuery.extend({}, right)  );
            } else {
                // send new data to the server
            }
            this.saveOnServer(right);
            $rootScope.$broadcast('right.update', {});
            $rootScope.$broadcast('rights.update' );
            $rootScope.$broadcast('form.to_regular_place');

            this.resetBackupUser();
        },
        saveOnServer: function(right) {
            API.saveOne(
                'right',
                null,
                {id : right.id, name : right.name, email : right.email},//TODO
                angular.toJson(service.rights),
                function(obj) {
                    if (obj.result === 'success') {
                        alert('Saved.');
                    } else {
                        alert('Error! No success message received.');
                    }
                }
            );
        },
        remove: function(index) {
            service.rights.splice(index, 1);
            this.saveOnServer();
            $rootScope.$broadcast( 'rights.update' );
        },
        edit: function(index) {
            console.log('------> edit');
            this.backupUser(index);
            $rootScope.$broadcast('right.update', service.rights[index]);
            $rootScope.$broadcast('form.to_fixed_position',service.rights[index]);
        },
        backupUser: function(index) {
            current_index = index;
            service.rights[index].backup = jQuery.extend({}, service.rights[index]);
            console.log(service.rights[current_index].backup);
        },
        cancel: function() {
            this.restoreRight();
            this.resetBackupRight();
            $rootScope.$broadcast('right.update', {});
            $rootScope.$broadcast( 'rights.update' );
            $rootScope.$broadcast('form.to_regular_place');
        },
        resetBackupRight: function() {
            if (current_index) {
                service.rights[current_index].backup = {};
                current_index = null;
            }
        },
        restoreRight: function() {
            if (
                typeof service.rights[current_index] !== 'undefined' &&
                    typeof service.rights[current_index].backup !== 'undefined'
                ) {
                service.rights[current_index] = service.rights[current_index].backup;
            }
        }
    }

    return service;
}]);