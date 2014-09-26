/**
 * Created by vadym on 7/7/14.
 */

'use strict';

app_module.service( 'Comment', [ '$rootScope','$http','API', function( $rootScope, $http, API ) {
    var current_index = null;
    var service = {
        comments: [],

        save: function ( comm, reqv_id ) {

            console.log('save() comm');
            console.log(comm);

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
            service.comments.splice(index, 1);
            this.saveOnServer();
            $rootScope.$broadcast( 'comments.update' );
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
            API.saveOne(
                'reqcomment',
                null,
                {id : comm.id, requirement_id : reqv_id},
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
                    service.comments = obj.data;
                    console.log(service.comments);
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