/**
 * Created by vadym on 7/16/14.
 */

'use strict';

app_module.controller(
    'forms',
            ['$scope','$document','$http','Settings',
    function ($scope,  $document,  $http, Settings) {

        Settings.getFromServer();

        $scope.account = {};
        $scope.formPersonal = app_module.base_url + 'js/ng/settings/templates/formPersonal.html';
        $scope.formPassword = app_module.base_url + 'js/ng/settings/templates/formPassword.html';
        $scope.formMail = app_module.base_url + 'js/ng/settings/templates/formMail.html';
        $scope.Settings = Settings;
        $scope.settings = Settings.settings;


        $scope.$on( 'settings.update', function( event, args ) {
            $scope.settings = args;
            $scope.account = $scope.settings[0];
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
    }]
);