/**
 * Created by alf on 7/25/14.
 */

'use strict';

app_module.service( 'Project', [ '$rootScope','$http','API', 'Participant', function( $rootScope, $http, API, Participant ) {
    var current_index = null;


    // PRIVATE method for object Project
    var getProjectParticipants = function() {
        angular.forEach(service.projects, function(pr,i){
            Participant.getFromServerByProject(pr);
        });
    }

    $rootScope.$on( 'projects.update', function( event ) {
        getProjectParticipants(); // call private method
    });

//    $rootScope.$on( 'quote.update', function( event ) {
//        console.log('---> update project participants Project.getProjectParticipants() (quote.update)');
//        getProjectParticipants(); // call private method
//    });

    $rootScope.$on( 'participants.update', function( event, participants, project_id ) {
        angular.forEach(service.projects,function(pr,count){
            if (pr.id == project_id) {
                service.projects[count].participants = participants;
                $rootScope.$broadcast( 'projects.participants.update' );
            }
        });
    });

    // Everything inside this block are PUBLIC properties and methods!!!
    var service = {
        projects: [],


        getSortedParticipants: function(projet_index, key_name, value_name) {
            var arr_to_return = [];
            angular.forEach(this.projects[projet_index].participants,function(item,index){
                var obj = {};
                obj[key_name] = item.id;
                obj[value_name] = item.user;
                arr_to_return.push(obj);
            });
            return arr_to_return;
        },

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
            this.restoreCanEdit();
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
//            if(!API.validateForm(project, ['name','client'], 'project_')) return false;

            this.restoreCanEdit();

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
            this.restoreCanEdit();
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
                    /**
                     * Actions to be performed on this broadcast message:
                     * - update EACH project participants
                     */
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
            this.backupCanEdit(index);
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
        },

        backupCanEdit: function(index) {
            if (index) {
                this.can_edit_projects_backup = null;
            } else {
                this.can_edit_projects_backup = this.can_edit_projects;
                this.can_edit_projects = this.can_add_projects;
            }
        },

        restoreCanEdit: function() {
            if (this.can_edit_projects_backup) {
                this.can_edit_projects = this.can_edit_projects_backup;
            }
        }
    }
    return service;
}]);