/**
 * Created by vadym on 7/7/14.
 */

'use strict';

app_module.service( 'Requirement', [ '$rootScope','$http', function( $rootScope, $http ) {
    var current_index = null;
    var service = {
        requirements: [],

        save: function ( reqv ) {

            console.log(reqv);

            if (typeof reqv.id === 'undefined' ) {
                service.requirements.push( jQuery.extend({}, reqv)  );
            } else {
                // send new data to the server
            }

            this.saveOnServer();
            $rootScope.$broadcast('reqv.update', {});
            $rootScope.$broadcast( 'requirements.update' );
            $rootScope.$broadcast('form.to_regular_place');

            this.resetBackupReqv();
        },
        remove: function(index) {
            service.requirements.splice(index, 1);
            this.saveOnServer();
            $rootScope.$broadcast( 'requirements.update' );
        },
        edit: function(index) {
            console.log('------> edit');
            this.backupReqv(index);
            $rootScope.$broadcast('reqv.update', service.requirements[index]);
            $rootScope.$broadcast('form.to_fixed_position',service.requirements[index]);
            //$rootScope.$broadcast( 'requirements.update' );
        },
        cancel: function() {
            console.log('------> cencel');
            this.restoreReqv();
            this.resetBackupReqv();
            $rootScope.$broadcast('reqv.update', {});
            $rootScope.$broadcast( 'requirements.update' );
            $rootScope.$broadcast('form.to_regular_place');
        },
        saveOnServer: function() {
            var url = this.prepareUrl('saveAll',{quote_id: app_module.quote_id});
            $http.post(url,angular.toJson(service.requirements))
                .success(function(data) {
                    console.log(data);
                })
                .error(function(data, status) {
                    console.log(data);
                    console.log(status);
                })
            ;
        },
        getFromServer: function() {
            var url = this.prepareUrl('getForQuote',{quote_id: app_module.quote_id});
            $http.get(url)
                .success(function(data) {
                    try {
                        var obj = angular.fromJson(data);
                    } catch (e) {
                        alert('Error! No data received.');
                    }
                    if (obj.result === 'success') {
                        service.requirements = obj.data;
                        $rootScope.$broadcast( 'requirements.update' );
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
            var url = app_module.base_url + app_module.prefix  + 'api/requirement/' + action + app_module.postfix;
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
            service.requirements[index].backup = jQuery.extend({}, service.requirements[index]);
            console.log(service.requirements[current_index].backup);
        },
        resetBackupReqv: function() {
            if (current_index) {
                service.requirements[current_index].backup = {};
                current_index = null;
            }
        },
        restoreReqv: function() {
            if (
                typeof service.requirements[current_index] !== 'undefined' &&
                typeof service.requirements[current_index].backup !== 'undefined'
            ) {
                service.requirements[current_index] = service.requirements[current_index].backup;
            }
        }
    }

  return service;
}]);