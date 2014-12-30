/**
 * Created by vadym on 7/7/14.
 */

'use strict';

app_module.service( 'Comment', [ '$rootScope','$http','API', function( $rootScope, $http, API ) {
    var current_index = null;
    var service = {
        comments: [],

        save: function ( comm, reqv_id ) {

            //console.log('save() comm');
            //console.log(comm);

//            if (typeof comm.id === 'undefined' ) {
//                service.comments.push( angular.clone(comm) );
//            } else {
//                // send new data to the server
//                service.comments.push( angular.clone(comm) );
//            }

            this.saveOnServer(comm,reqv_id);
//            $rootScope.$broadcast('comm.update', {});
//            $rootScope.$broadcast( 'comments.update' );
//
//            this.resetBackupComm();
        },
        remove: function(index) {
            console.log(index);
            service.comments.splice(index, 1);
            this.saveOnServer();
            $rootScope.$broadcast( 'comments.update' );
        },
        delete: function(id) {
            API.removeOne(
                'reqcomment',
                null,
                {'id' : id},
                function(obj) {
                    if (obj.result === 'success') {
                        $rootScope.$broadcast( 'comments.need_update' );
                    } else {
                    }
                }
            );
        },
        edit: function(index) {
            console.log('------> edit');
            this.backupComm(index);
            $rootScope.$broadcast('comm.update', service.comments[index]);
            //$rootScope.$broadcast( 'comments.update' );
        },
        cancel: function(comm) {
            console.log('------> cencel');
            this.restoreComm();
            this.resetBackupComm();
            $rootScope.$broadcast('comm.update', {});
            $rootScope.$broadcast( 'comments.update' );
        },
        saveOnServer: function(comm,reqv_id) {
            comm.requirement_id = reqv_id;
            API.saveOne(
                'reqcomment',
                null,
                {id : comm.id},
                angular.toJson(comm),
                function(obj) {
                    $rootScope.$broadcast('comments.need_update' );
                }
            );
        },
        getFromServer: function(reqv_id) {
            API.getAll(
                'reqcomment',
                'getByField',
                {field:'requirement_id',value:reqv_id},
                function(obj) {
                    // Delete button
                    if(obj.result === 'success'){
                        $.each(obj.data, function(index, value) {
                            if(value.user_id != app_module.user_id) {
                                obj.data[index]['allow_del_css'] = 'display: none;';
                            }
                        });
                        service.comments = obj.data;
                    }
                    $rootScope.$broadcast( 'comments.update' );
                }
            );
        },
        backupComm: function(index) {
            current_index = index;
            service.comments[index].backup = angular.copy( service.comments[index]);
        },
        resetBackupComm: function() {
            if (current_index) {
                service.comments[current_index].backup = {};
                current_index = null;
            }
        },
        restoreComm: function() {
            service.comments[current_index] = service.comments[current_index].backup;
        }
    }

  return service;
}]);