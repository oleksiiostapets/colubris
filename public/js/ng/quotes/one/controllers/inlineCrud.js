/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.controller(
    'inlineCrud',
            ['$scope','$document','$http','Requirement','Comment','Task','Quote',
    function ($scope,  $document,  $http,  Requirement,  Comment,  Task, Quote) {

   // quote info
    Quote.getFromServer();

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

    Requirement.getFromServer();

    $scope.$on( 'quote.update', function( event) {
        $scope.quote = Quote.quote;
    });
    $scope.$on( 'reqv.update', function( event, args ) {
        $scope.reqv = args;
    });
    $scope.$on( 'requirements.update', function( event ) {
        $scope.requirements = Requirement.requirements;
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
            });
        });
    });
    $scope.$on( 'tasks.update', function( event ) {
        $scope.tasks = Task.tasks;
    });

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
        console.log('### toggleIsIncluded');
        $scope.$broadcast('checkbox.update.'+args.id,args);
        Requirement.saveRequirementOnServer(args);
        var reqv_cloned = jQuery.extend({}, args);
    }
}])
;