'use strict';

// Parent controller — recebe userData via resolve do state e o expõe para os filhos.
// Os controllers de cada tab leem $scope.user e podem chamar $scope.reloadUser() para refrescar.
angular.module('yiiApp').controller('UserEditCtrl',
    ['$scope', '$state', '$stateParams', 'userData', 'userService',
        function ($scope, $state, $stateParams, userData, userService) {

            $scope.user = userData;
            $scope.userId = parseInt($stateParams.id, 10);

            $scope.reloadUser = function () {
                return userService.findById($scope.userId).then(function (data) {
                    $scope.user = data;
                    return data;
                });
            };

            $scope.backToList = function () { $state.go('users'); };
        }]);
