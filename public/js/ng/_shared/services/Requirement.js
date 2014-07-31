/**
 * Created by alf on 7/25/14.
 */

'use strict';

app_module.service( 'Requirement', [ '$rootScope','$http','API', function( $rootScope, $http, API ) {
    var current_index = null;
    var service = {
        requirements: [],

        clear: function(){
            service.requirements = [];
            $rootScope.$broadcast( 'requirements.update' );
        },
        getFromServerByQuote: function(quote) {
            // Clearing previous requirement parameters to exclude it from crud results
            var params = {};
            $.each($rootScope.filter_values,function(key,value) {
                if (key != 'requirement_id') params[key]=value;
            });
            $rootScope.filter_values = params;

            API.getAll(
                'requirement',
                undefined,
                {field:'quote_id',value:quote.id},
                function(obj) {
                    service.requirements = obj.data;
                    $rootScope.$broadcast( 'requirements.update' );
                }
            );
        },
        save: function ( reqv ) {

            console.log('### Requirement.save');
            console.log(reqv);

            if (typeof reqv.id === 'undefined' ) {
                service.requirements.push( angular.copy(reqv)  );
            }

            API.saveOne(
                'requirement',
                null,
                {id : reqv.id},
                angular.toJson(reqv),
                function(obj) {
                    /*if (obj.result === 'success') {
                        $rootScope.showSystemMsg('saved');
                    } else {
                        alert('Error! No success message received.');
                    }*/
                    $rootScope.$broadcast('reqv.update', {});
                    $rootScope.$broadcast( 'requirements.update' );
                    $rootScope.$broadcast('form.to_regular_place');
                }
            );
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
            $rootScope.$broadcast( 'requirements.update' );
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
        saveRequirementOnServer: function(reqv) {
            API.saveOne(
                'requirement',
                null,
                {id: reqv.id, is_included: reqv.is_included},
                angular.toJson(service.requirements)
            );
        },
        getFromServer: function() {
            API.getAll(
                'requirement',
                'getForQuote',
                {quote_id: app_module.quote_id},
                function(obj) {
                    service.requirements = obj.data;
                    $rootScope.$broadcast( 'requirements.update' );
                }
            );
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
            service.requirements[index].backup = angular.copy( service.requirements[index]);
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