/**
 * Created by vadym on 7/7/14.
 */

'use strict';

app_module.service( 'Requirement', [ '$rootScope','$http', function( $rootScope, $http ) {
    var service = {
        requirements: [],

        save: function ( req ) {

            console.log(req);

            if (typeof req.id === 'undefined' ) {
                service.requirements.push( jQuery.extend({}, req)  );
            } else {
                // send new data to the server
            }

            this.saveOnServer();
            $rootScope.$broadcast('reqv.update', {});
            $rootScope.$broadcast( 'requirements.update' );

            console.log(app_module.base_url);
        },
        remove: function(index) {
            service.requirements.splice(index, 1);
            this.saveOnServer();
            $rootScope.$broadcast( 'requirements.update' );
        },
        edit: function(index) {
            console.log(index);
            console.log(service.requirements[index]);
            $rootScope.$broadcast('reqv.update', service.requirements[index]);
            //$rootScope.$broadcast( 'requirements.update' );
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
        }
    }

  return service;
}]);