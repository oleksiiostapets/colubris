/**
 * Created by alf on 7/25/14.
 */

'use strict';

app_module.service( 'Project', [ '$rootScope','$http','API', function( $rootScope, $http, API ) {
    var current_index = null;
    var service = {
        projects: [],

        remove: function(index) {
            try {
                API.removeOne(
                    'project',
                    'deleteById',
                    {id:service.projects[index].id},
                    function(obj) {
                        if (obj.result === 'success') {
                            service.projects.splice(index, 1);
                            $rootScope.$broadcast( 'projects.update' );
                        } else {
                            alert('Error! No success message received.');
                        }
                    }
                );
            } catch (e) {
                alert('Error! No data received.');
            }
        },
        delete: function(id) {
            API.removeOne(
                'project',
                null,
                {'id' : id},
                function(obj) {
                    if (obj.result === 'success') {
                        $rootScope.$broadcast('project.update', {});
                        $rootScope.$broadcast('form.to_regular_place');
                        $rootScope.$broadcast( 'projects.need_update' );
                    } else {
                    }
                }
            );
        },
        clear: function(){
            service.projects = [];
            $rootScope.$broadcast( 'projects.update' );
        },

        save: function ( project) {

//            console.log(project);
            if(!API.validateForm(project, ['name','client'], 'project_')) return false;

            if (typeof project.id === 'undefined' ) {
                service.projects.push( angular.copy(project));
            } else {
                // refresh crud data on the client
                if(current_index){
                    service.projects[current_index]=project;
                }
                // send new data to the server
            }
            this.saveOnServer(project);
            this.resetbackupProject();
            $rootScope.$broadcast('project.update', {});
        },

        edit: function(index) {
            console.log('------> edit');
            this.backupProject(index);
            $rootScope.$broadcast('project.update', service.projects[index]);
            $rootScope.$broadcast('form.to_fixed_position',service.projects[index]);
            console.log('------> edit');
        },

        cancel: function() {
            console.log('------> cancel');
            this.restoreProject();
            this.resetbackupProject();
            $rootScope.$broadcast('project.update', {});
            $rootScope.$broadcast( 'projects.update' );
            $rootScope.$broadcast('form.to_regular_place');
            console.log('------> cancel');
        },

        getFromServer: function(field,value) {

            var field_val = field ? {field:field, value:value} : {};

            API.getAll(
                'project',
                undefined,
                field_val,
                function(obj) {
                    service.projects = obj.data;
                    $rootScope.$broadcast( 'projects.update' );
                }
            );
        },

        saveOnServer: function(project) {
            API.saveOne(
                'project',
                null,
                {id : project.id},
                angular.toJson(project),
                function(obj) {
                    $rootScope.$broadcast('projects.need_update' );
                    $rootScope.$broadcast('form.to_regular_place');
                }
            );
        },

        showForm: function(index) {
            console.log('------> Show');
            $rootScope.$broadcast('form.to_fixed_position',service.projects[index]);
        },

        backupProject: function(index) {
            current_index = index;
            service.projects[index].backup = angular.copy( service.projects[index]);
        },

        resetbackupProject: function() {
            if (current_index) {
                service.projects[current_index].backup = {};
                current_index = null;
            }
        },

        restoreProject: function() {
            if (
                  current_index &&
                  angular.isDefined(service.projects[current_index]) &&
                      angular.isDefined(service.projects[current_index].backup)
                ) {
                service.projects[current_index] = service.projects[current_index].backup;
            }
        }
    }
    return service;
}]);