/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.controller(
    'inlineCrud',
            ['$scope','$document','$http','Requirement','Comment','Task','Quote',
    function ($scope,  $document,  $http,  Requirement,  Comment,  Task, Quote) {

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

    Requirement.getFromServer();

    $scope.$on( 'quote.update', function( event) {
        $scope.quote = Quote.quotes;
    });
    $scope.$on( 'reqv.update', function( event, args ) {
        $scope.reqv = args;
    });
    $scope.$on( 'requirements.update', function( event ) {
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
        if($scope.calc.spent > $scope.calc.total){
            $scope.calc.color = 'atk-effect-danger';
        }else{
            $scope.calc.color = 'atk-effect-success';

        }
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