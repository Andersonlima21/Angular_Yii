'use strict';

angular.module('yiiApp').controller('UserTabInfoCtrl',
    ['$scope', 'userService',
        function ($scope, userService) {

            $scope.form = {
                name: $scope.user.name,
                email: $scope.user.email
            };
            $scope.saving = false;
            $scope.message = null;
            $scope.error = null;

            $scope.save = function () {
                $scope.saving = true;
                $scope.message = null;
                $scope.error = null;

                userService.update($scope.userId, $scope.form).then(function (msg) {
                    $scope.message = msg;
                    return $scope.reloadUser();
                }).catch(function (err) {
                    $scope.error = err.message;
                }).finally(function () {
                    $scope.saving = false;
                });
            };
        }]);
