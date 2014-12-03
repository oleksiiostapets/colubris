/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.controller(
    'inlineCrud',
    ['$scope','$document','$http','Requirement','Comment','Task','Quote','User',
        function ($scope,  $document,  $http,  Requirement,  Comment,  Task,  Quote,  User) {

            // quote info
            Quote.getOneFromServer(app_module.quote_id);

            $scope.calc = {};

            // reqv
            $scope.reqv = {};
            $scope.Requirement = Requirement;
            $scope.requirements = Requirement.requirements;

            // comm
            $scope.Comment = Comment;
            $scope.comments = Comment.comments;
            $scope.actionButtonSet = {};

            // task
            $scope.Task = Task;
            $scope.tasks = Task.tasks;
            $scope.actionButtonSet = {};

            $scope.priorities = [
                {name:'low'},
                {name:'normal'},
                {name:'high'}
            ];
            $scope.priority = $scope.priorities;

            $scope.types = [
                {name:'Project',        value:'project'},
                {name:'Change request', value:'change_request'},
                {name:'Bug',            value:'bug'},
                {name:'Support',        value:'support'},
                {name:'Drop',           value:'drop'}
            ];
            $scope.type = $scope.types;

            $scope.statuses = [
                {name:'unstarted'},
                {name:'started'},
                {name:'finished'},
                {name:'tested'},
                {name:'rejected'},
                {name:'accepted'}
            ];
            $scope.status = $scope.statuses;

            $scope.User = User;
            $scope.requesters = User.users;
            User.getFromServer('requesters.update','getAllUsers');
            $scope.assigneds = User.users;
            User.getFromServer('assigneds.update','getAllUsers');

            Requirement.getFromServer();

            $scope.$on( 'assigneds.update', function( event ) {
                $scope.assigneds = User.users;
                $scope.assigneds.unshift({id:"",email:'all'})
            });

            $scope.$on( 'requesters.update', function( event ) {
                $scope.requesters = User.users;
                $scope.requesters.unshift({id:"",email:'all'})
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
            });
            $scope.$on( 'reqv_tasks.update', function( event, args ) {
                $scope.tasks = args;
            });
            $scope.$on( 'reqv.update', function( event, args ) {
                $scope.reqv = args;
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
            }

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