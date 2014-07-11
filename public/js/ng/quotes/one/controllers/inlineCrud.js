/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.controller(
    'inlineCrud',
            ['$scope','$document','$http','Requirement','Comment',
    function ($scope,  $document,  $http,  Requirement,  Comment) {

    $scope.form = app_module.base_url + 'js/ng/quotes/one/templates/form.html';
    $scope.crud = app_module.base_url + 'js/ng/quotes/one/templates/crud.html';
    $scope.comment_list = app_module.base_url + 'js/ng/quotes/one/templates/comment-list.html';
    $scope.task_list = app_module.base_url + 'js/ng/quotes/one/templates/task-list.html';

    // reqv
    $scope.reqv = {};
    $scope.Requirement = Requirement;
    $scope.requirements = Requirement.requirements;

    // comm
    $scope.Comment = Comment;
    $scope.comments = Comment.comments;
    $scope.actionButtonSet = {};

    Requirement.getFromServer();

    $scope.$on( 'reqv.update', function( event, args ) {
        $scope.reqv = args;
    });
    $scope.$on( 'requirements.update', function( event ) {
        $scope.requirements = Requirement.requirements;
    });
    $scope.$on( 'comments.update', function( event ) {
        $scope.comments = Comment.comments;
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
}])
;