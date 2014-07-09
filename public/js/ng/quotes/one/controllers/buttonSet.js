/**
 * Created by vadym on 6/13/14.
 */

'use strict';

app_module.controller('buttonSet', ['$scope','$http','Requirement', function ($scope,$http,Requirement) {
    //console.log($scope);
    $scope.Requirement = Requirement;
    $scope.requirements = Requirement.requirements;
    $scope.actionButtonSet = {};
}])
;