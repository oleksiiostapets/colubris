/**
 * Created by alf on 7/25/14.
 */

'use strict';

app_module.service( 'Requirement', [ '$rootScope','$http','API', function( $rootScope, $http, API ) {
//    var current_index = null;
    var service = {
        current_index: null,
        requirements: [],

        clear: function(){
            service.requirements = [];
            $rootScope.$broadcast( 'requirements.update' );
            $rootScope.$broadcast('task.clear');
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
            console.log('----->Save()');
            var args = angular.copy(reqv);

            args.quote_id = app_module.quote_id;
            args.user_id = app_module.user_id;

            if(!angular.isDefined(args.is_included)){
                args.is_included = true;
            }
            if (args.is_included === true) {
                args.is_included = 1;
            } else {
                args.is_included = 0;
            }

            if (typeof args.id === 'undefined' ) {
//                service.requirements.push( angular.copy(reqv)  );//Moved to API.saveOne()
            } else {
                $.each(service.requirements,function(i,e){
                    if (e.id == args.id) {
                        service.requirements[i] = args;
                    }
                });
            }
            //console.log(angular.toJson(args));
            API.saveOne(
                'requirement',
                null,
                {id : args.id},
                angular.toJson(args),
                function(obj) {
                    if (obj.result === 'success') {
                        //$rootScope.showSystemMsg('Saved successfully');
                        if (typeof args.id === 'undefined' ) {
                            service.requirements.push( angular.copy(obj.data)  );
                        }
                    } else {
                        //$rootScope.showSystemMsg('Error! No success message received');
                    }
                    $rootScope.$broadcast( 'requirements.update' );
                    $rootScope.$broadcast( 'checkbox.update.'+args.id,reqv );
                    //$rootScope.$broadcast( 'requirements.reload' );
                    $rootScope.$broadcast( 'form.to_regular_place' );

                }
            );
            this.resetBackupReqv();
            //$rootScope.$broadcast( 'requirements.update' );
            //$rootScope.$broadcast( 'checkbox.update.'+args.id,reqv);
        },
        remove: function(index) {
            API.removeOne(
                'requirement',
                null,
                {'id' : service.requirements[index].id},
                function(obj) {
                    if (obj.result === 'success') {
//                    console.log(data);
                    } else {
//                    console.log(data);
//                    console.log(status);
                    }
                }
            );
            service.requirements.splice(index, 1);
//            this.saveOnServer();
            $rootScope.$broadcast( 'requirements.update' );
        },
        edit: function(index) {
            console.log('------> edit');
            this.backupReqv(index);
            $rootScope.$broadcast('reqv.update', service.requirements[index]);
            $rootScope.$broadcast('form.to_fixed_position',service.requirements[index]);
            $rootScope.$broadcast( 'requirements.update' );
        },
        showForm: function(index) {
            console.log('------> Show');
            $rootScope.$broadcast('form.to_fixed_position',service.requirements[index]);
        },
        cancel: function() {
            console.log('------> cencel');
            this.restoreReqv();
            this.resetBackupReqv();
            $rootScope.$broadcast('reqv.update', {});
            $rootScope.$broadcast( 'requirements.update' );
            $rootScope.$broadcast('form.to_regular_place');
            $rootScope.$broadcast('task.clear');
            $rootScope.$broadcast('comm.clear');
        },
        getFromServer: function(quote) {
            API.getAll(
                'requirement',
                'getForQuote',
                {quote_id: app_module.quote_id},
                function(obj) {
                    $.each(obj.data,function(i,e){
                        var spent_time = '';
                        var cost = '';
                        var del = '';
                        if(app_module.current_user_rights.indexOf('can_see_spent_time') != -1){
                            spent_time = e.spent_time;
                        }
                        if(app_module.current_user_rights.indexOf('can_see_finance') != -1){
                            cost = Math.ceil(e.estimate * quote.quotes[0].calc_rate) + quote.quotes[0].currency;
                        }
                        if(spent_time != '' && cost != ''){
                            del = ' / ';
                        }
                        obj.data[i].spent_time_cost = spent_time + del + cost;

                    });
                    var spent_time_text = '';
                    var cost_text = '';
                    var del = '';
                    if(app_module.current_user_rights.indexOf('can_see_spent_time') != -1){
                        spent_time_text = 'Spent time';
                    }
                    if(app_module.current_user_rights.indexOf('can_see_finance') != -1){
                        cost_text = 'Cost';
                    }
                    if(spent_time_text != '' && cost_text != ''){
                        del = ' / ';
                    }
                    obj.data.spent_time_cost_text = spent_time_text +del + cost_text;

                    service.requirements = obj.data;
                    $rootScope.$broadcast( 'requirements.update' );
                }
            );
        },
        backupReqv: function(index) {
            this.current_index = index;
            service.requirements[index].backup = angular.copy(service.requirements[index]);
            //console.log(service.requirements[this.current_index].backup);
        },
        resetBackupReqv: function() {
            if (this.current_index) {
                service.requirements[this.current_index].backup = {};
                this.current_index = null;
            }
        },
        restoreReqv: function() {
            if (
                typeof service.requirements[this.current_index] !== 'undefined' &&
                typeof service.requirements[this.current_index].backup !== 'undefined'
            ) {
                service.requirements[this.current_index] = service.requirements[this.current_index].backup;
            }
        }
    };
    return service;
}]);