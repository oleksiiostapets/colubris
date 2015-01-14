/**
 * Created by alf on 7/25/14.
 */

'use strict';

app_module.service( 'Quote', [ '$rootScope','$http','API', function( $rootScope, $http, API ) {
    var current_index = null;
    var service = {
        quotes: [],
        statuses: [
            {key: 'quotation_requested', value: 'Quotation requested'},
            {key: 'estimate_needed', value: 'Estimate needed'},
            {key: 'not_estimated', value: 'Not estimated'},
            {key: 'estimation_approved', value: 'Estimation approved'},
            {key: 'finished', value: 'Finished'}
        ],

        getStatusValueByKey: function(key) {
            var return_value = false;
            angular.forEach(this.statuses,function(status,index){
                if (status.key === key) {
                    return_value = status.value;
                }
            });
            return return_value;
        },
        getStatusKeyByValue: function(value) {
            var return_key = false;
            angular.forEach(this.statuses,function(status,index){
                if (status.value === value) {
                    return_key = status.key;
                }
            });
            return return_key;

        },
        getStatuses: function(key_name, value_name) {
            var arr_to_return = [];
            angular.forEach(this.statuses,function(status,index){
                var obj = {};
                obj[key_name] = status.key;
                obj[value_name] = status.value;
                arr_to_return.push(obj);
            });
            return arr_to_return;
        },

        clear: function(){
            service.quotes = [];
            $rootScope.$broadcast( 'quotes.update' );
        },
        getFromServerByProject: function(project) {
            // Clearing previous quote and requirement parameters to exclude it from crud results
            var params = {};
            if(!project)return;
            $.each($rootScope.filter_values,function(key,value) {
                if (key != 'quote_id' && key != 'requirement_id') params[key]=value;
            });
            $rootScope.filter_values = params;

            API.getAll(
                'quote',
                undefined,
                {field:'project_id',value:project.id},
                function(obj) {
                    $.each(obj.data,function(key,value) {
                        if(app_module.current_user_rights.indexOf('can_delete_quote') == -1){
                            obj.data[key]['allow_del_css'] = 'display: none;';
                        }
                        if(value.user_id != app_module.user_id) {
                            obj.data[key]['allow_del_css'] = 'display: none;';
                        }
                        if (value.is_archived == 1) {
                            var b = true;
                        }
                        if (value.is_archived == 0) {
                            var b = false;
                        }
                        obj.data[key].is_archived_boolean = b;
                    });
                    service.quotes = obj.data;
                    $rootScope.$broadcast( 'quotes.update', project );
                }
            );
        },
        getOneFromServer: function(quote_id){
            var field_val = (quote_id)? {id: quote_id} : {};
            API.getOne(
                'quote',
                undefined,
                field_val,
                function(obj) {
                    service.quotes = obj.data;
                    $rootScope.$broadcast( 'quote.update' );
                }
            );
        },

        save: function ( quote, project ) {
            if (typeof quote === 'undefined') {
                alert('Cannot save');
                return;
            }

            if (typeof quote.is_archived_boolean !== 'undefined') {
                if (quote.is_archived_boolean === true) {
                    quote.is_archived = 1;
                }
                if (quote.is_archived_boolean === false) {
                    quote.is_archived = 0;
                }
            }

            //Add project_id field
            quote.project_id = project.id;

            //Formatting dates for update
            if(quote.id){
                var deadline_month = quote.deadline_obj.getMonth()+1;
                if(deadline_month < 10) deadline_month = '0' + deadline_month;
                quote.deadline = quote.deadline_obj.getFullYear()+'-'+deadline_month+'-'+quote.deadline_obj.getDate();

                var warranty_month = quote.warranty_end_obj.getMonth()+1;
                if(warranty_month < 10) warranty_month = '0' + warranty_month;
                quote.warranty_end = quote.warranty_end_obj.getFullYear()+'-'+warranty_month+'-'+quote.warranty_end_obj.getDate();
            }


            //if(!API.validateForm(task, ['name','client'], 'project_')) return false;
            API.saveOne(
                'quote',
                null,
                {id : quote.id},
                angular.toJson(quote),
                function(obj) {
                    $rootScope.$broadcast('quotes.need_update', project );
                    //$rootScope.$broadcast('form.to_regular_place');
                }
            );
            //this.resetbackupQuote();
            $rootScope.$broadcast('quote.update', {});
            $rootScope.$broadcast('quote_form.clear');
        },
        remove: function(id,project) {
            try {
                API.removeOne(
                    'quote',
                    'deleteById',
                    {id:id},
                    function(obj) {
                        if (obj.result === 'success') {
                            $rootScope.$broadcast( 'quotes.update');
                            $rootScope.$broadcast('quotes.need_update', project );
                        } else {
                            console.log(obj);
                            alert('Error! No success message received.');
                        }
                    }
                );
            } catch (e) {
                console.log(e);
                alert('Error! No data received.');
            }
        }
    };
    return service;
}]);