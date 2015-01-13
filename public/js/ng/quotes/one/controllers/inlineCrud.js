/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.controller(
    'inlineCrud',
    ['$scope','$document','$http','Requirement','Comment','Task','Quote','User','Project',
        function ($scope,  $document,  $http,  Requirement,  Comment,  Task,  Quote,  User, Project) {

            // quote info
            $scope.Quote = Quote;
            Quote.getOneFromServer(app_module.quote_id);
            if(app_module.current_user_rights.indexOf('can_see_finance') != -1){
                //user can see money
                Quote.can_see_finance = 'display:block;';
            }else{
                Quote.can_see_finance = 'display:none;';
            }
            if(app_module.current_user_rights.indexOf('can_see_spent_time') != -1){
                //user can see spent time
                Quote.can_see_spent_time = 'display:block;';
            }else{
                Quote.can_see_spent_time = 'display:none;';
            }
            if(app_module.current_user_rights.indexOf('can_see_rates') != -1){
                //user can see spent time
                Quote.can_see_rates = 'display:block;';
            }else{
                Quote.can_see_rates = 'display:none;';
            }




            $scope.calc = {};

            // reqv
            $scope.reqv = {};
            $scope.Requirement = Requirement;
            $scope.requirements = Requirement.requirements;

            if(app_module.current_user_rights.indexOf('can_add_requirement') != -1){
                //Quote.can_add_requirement = 'display:block;';
            }else{
                Requirement.can_add_requirement = 'display:none;';
            }

            if(app_module.current_user_rights.indexOf('can_edit_requirement') != -1){
                //Quote.can_add_requirement = 'display:block;';
            }else{
                Requirement.can_edit_requirement = 'display:none;';
            }

            if(app_module.current_user_rights.indexOf('can_delete_requirement') != -1){
                //Quote.can_add_requirement = 'display:block;';
            }else{
                Requirement.can_delete_requirement = 'display:none;';
            }

            if(
                app_module.current_user_rights.indexOf('can_edit_requirement') == -1
                &&
                app_module.current_user_rights.indexOf('can_delete_requirement') == -1
            ){
                Requirement.show_actions = 'display:none;';
            }

            // comm
            $scope.Comment = Comment;
            $scope.comments = Comment.comments;
            $scope.actionButtonSet = {};

            // task
            $scope.Task = Task;
            $scope.tasks = Task.tasks;
            $scope.actionButtonSet = {};

            if(app_module.current_user_rights.indexOf('can_add_task') != -1){
                //Task.can_add_task = 'display:block;';
            }else{
                Task.can_add_task = 'display:none;';
            }

            // TODO move to Task service
            $scope.priorities = [
                {name:'low'},
                {name:'normal'},
                {name:'high'}
            ];
            $scope.priority = $scope.priorities; // TODO why two variables with exactly same values

            // TODO move to Task service
            $scope.types = [
                {name:'Project',        value:'project'},
                {name:'Change request', value:'change_request'},
                {name:'Bug',            value:'bug'},
                {name:'Support',        value:'support'},
                {name:'Drop',           value:'drop'}
            ];
            $scope.type = $scope.types; // TODO why two variables with exactly same values

            // TODO move to Task service
            $scope.statuses = [
                {name:'unstarted'},
                {name:'started'},
                {name:'finished'},
                {name:'tested'},
                {name:'rejected'},
                {name:'accepted'}
            ];
            $scope.status = $scope.statuses; // TODO why two variables with exactly same values

            // User
            $scope.User = User;

            // Requirements
            Requirement.getFromServer(Quote);




            $scope.$on( 'assigneds.update', function( event ) {
                $scope.assigneds = User.users;
            });
            $scope.$on( 'requesters.update', function( event ) {
                $scope.requesters = User.users;
            });

            $scope.$on( 'comments.need_update', function( event, args ) {
                Comment.getFromServer($scope.Requirement.requirements[$scope.Requirement.current_index].id);
            });
            $scope.$on( 'task.clear', function( event ) {
                $scope.tasks = {};
                $('#task_view textarea, #task_view input').val('');
            });
            $scope.$on( 'comm.clear', function( event ) {
                $scope.comments = {};
                $('#comment_view textarea, #comment_view input').val('');
            });
            $scope.$on( 'tasks.need_update', function( event, args ) {
                Task.getFromServerByReqvId($scope.Requirement.requirements[$scope.Requirement.current_index].id);
            });
            $scope.$on( 'quote.update', function( event) {
                $scope.quote = Quote.quotes;
                $scope.quote[0].deadline_obj = new Date($scope.quote[0].deadline);
                $scope.quote[0].warranty_end_obj = new Date($scope.quote[0].warranty_end);

                $scope.Project = Project;
                Project.getFromServer('id',$scope.quote[0].project_id);

            });
            $scope.$on('projects.update', function(event){
                $scope.projects = Project.projects;
            });
            $scope.$on( 'reqv_tasks.update', function( event, args ) {
                $scope.tasks = args;
            });
            $scope.$on( 'reqv.update', function( event, args ) {
                $scope.reqv = args;

                User.getFromServerByProjectId(args.project_id,['requesters.update','assigneds.update']);
            });
            $scope.$on( 'requirements.reload', function( event ) {
                $scope.requirements = Requirement.requirements;
                Requirement.getFromServer();
            });
            $scope.$on( 'requirements.update', function( event ) {
                console.log('requirements.update - start');
                $scope.requirements = Requirement.requirements;
                $scope.calc.net = 0;
                $scope.calc.spent = 0;
                $.each($scope.requirements,function(key,value){
                    if(value.is_included == 1 && value.estimate!=null){
                        $scope.calc.net = $scope.calc.net + parseFloat(value.estimate);
                    }
                    if(parseFloat(value.spent_time)){
                        $scope.calc.spent = $scope.calc.spent + parseFloat(value.spent_time);
                    }
                });
                $scope.calc.subtotal = $scope.calc.net;
                $scope.calc.pm = Math.ceil($scope.calc.subtotal/5);
                $scope.calc.total = $scope.calc.subtotal + $scope.calc.pm;
                $scope.calc.spent = Math.ceil($scope.calc.spent);
                if($scope.calc.spent > $scope.calc.total){
                    $scope.calc.color = 'atk-effect-danger';
                }else{
                    $scope.calc.color = 'atk-effect-success';

                }
                console.log('requirements.update - end');
            });
            $scope.$on( 'comments.update', function( event ) {
                $scope.comments = Comment.comments;
                $.each($scope.comments,function(key,value) {
                    $.each(value,function(key2,value2) {
                        if(key2 == 'user_avatar_thumb'){
                            if(value2 == null){
                                $scope.comments[key][key2] = 'http://' + document.domain + window.location.pathname + 'images/no-user-image.gif';
                            }else{
                                $scope.comments[key][key2] = 'http://' + document.domain + window.location.pathname + 'upload/' + value2;
                            }
                        }
                        if(key2 == 'file_thumb'){
                            if(value2 != null){
                                $scope.comments[key][key2] = 'http://' + document.domain + window.location.pathname + value2;
                            }
                        }
                    });
                });
            });
            //    $scope.$on( 'tasks.update', function( event ) {
            //        $scope.tasks = Task.tasks;
            //    });

            // ESC key close requirement div
            $(document).on('keydown', function(evt){
                evt = evt || window.event;
                if (evt.keyCode == 27) {
                    $('#close-button').trigger('click');
                }
            });

            $scope.toggle = function(show,hide) {
                $('#'+show).removeClass('ui-helper-hidden');
                $('#'+hide).addClass('ui-helper-hidden');
            };

            $scope.toggleIsIncluded = function(args){
                var reqv = angular.copy(args);
                reqv.is_included = !reqv.is_included;
                args = reqv;
                Requirement.save(args);



                ///   floating total
                if(args.is_included == true){
                    $scope.calc.net = $scope.calc.net + parseFloat(args.estimate);
                }else{
                    $scope.calc.net = $scope.calc.net - parseFloat(args.estimate);
                }
                $scope.calc.subtotal = $scope.calc.net;
                $scope.calc.pm = Math.ceil($scope.calc.subtotal/5);
                $scope.calc.total = $scope.calc.subtotal + $scope.calc.pm;
                var reqv_cloned = angular.copy( args);
            }
        }])
;