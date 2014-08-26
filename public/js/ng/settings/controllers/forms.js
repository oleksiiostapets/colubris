/**
 * Created by vadym on 7/16/14.
 */

'use strict';

app_module.controller(
    'forms',
            ['$scope','$document','$http','Setting','$rootScope',
    function ($scope,  $document,  $http,  Setting , $rootScope) {

        Setting.getFromServer();

        $scope.account = {};
        $scope.formAvatar = app_module.base_url + 'js/ng/settings/templates/formAvatar.html';
        $scope.formPersonal = app_module.base_url + 'js/ng/settings/templates/formPersonal.html';
        $scope.formPassword = app_module.base_url + 'js/ng/settings/templates/formPassword.html';
        $scope.formMail = app_module.base_url + 'js/ng/settings/templates/formMail.html';
        $scope.Setting = Setting;
        $scope.settings = Setting.settings;

        $scope.upload = function() {
            var url = app_module.base_url + app_module.prefix  + 'api/account/' + 'addToFilestore' + app_module.postfix;
            var fd = new FormData();
            fd.append('file',document.getElementById('file').files[0]);
            fd.append('id',$scope.account.id);
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4) {
                    try {
                        var obj = angular.fromJson(xhr.responseText);
                    } catch (e) {
                        alert('Error! No data received.');
                    }
                    if (obj.result === 'success') {
                        $("#avatar").html('<img src="http://' + document.domain + obj.data.thumb_url + '" alt="Avatar" />');
                    } else {
                        alert('Error! No success message received.');
                    }
                }
            }
            xhr.open("POST", url);
            xhr.send(fd);
        };

        $scope.$on( 'settings.update', function( event, args ) {
            $scope.settings = args;
            $scope.account = $scope.settings[0];
            //console.log($scope.account.avatar_thumb);
            if($scope.account.avatar_thumb != '' && $scope.account.avatar_thumb != null) $("#avatar").html('<img src="http://' + document.domain + window.location.pathname + 'upload/' + $scope.account.avatar_thumb + '" alt="Avatar" />');
            //console.log($scope.account);
        });
        $scope.$on( 'settings.message', function( event, args ) {
            $scope.settings = args;
            $scope.account = $scope.settings[0];
        });
        $scope.$on( 'settings.password_changed', function( event, args ) {
            $scope.account.old_password = '';
            $scope.account.new_password = '';
            $scope.account.verify_password = '';
        });



        $rootScope.hideTag = function(tag_id,time) {
            setTimeout(function(){
                $(tag_id).hide();
            }, time);
        }
        $rootScope.removeTag = function(tag_id,time) {
            setTimeout(function(){
                $(tag_id).remove();
            }, time);
        }
        $rootScope.showSystemMsg = function(msg) {
            $("#msg").html(msg);
            $("#msg").show();
            $rootScope. hideTag("#msg", 3000);
        }
    }]
);