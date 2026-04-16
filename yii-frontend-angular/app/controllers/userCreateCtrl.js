'use strict';

angular.module('yiiApp').controller('UserCreateCtrl',
    ['$scope', '$state', 'userService',
        function ($scope, $state, userService) {

            $scope.form = { name: '', email: '' };
            $scope.saving = false;
            $scope.error = null;

            $scope.cancel = function () { $state.go('users'); };

            $scope.submit = function () {
                $scope.saving = true;
                $scope.error = null;

                userService.create($scope.form).then(function () {
                    // O backend devolve apenas uma string de sucesso, sem o id.
                    // Para entrar na tela de edição precisamos descobrir o id pelo email (que é unique).
                    return userService.findAll();
                }).then(function (list) {
                    var created = (list || []).filter(function (u) {
                        return u.email === $scope.form.email;
                    })[0];
                    if (!created) {
                        throw new Error('Usuário criado, mas não foi possível localizá-lo na listagem.');
                    }
                    $state.go('editUser.info', { id: created.id });
                }).catch(function (err) {
                    $scope.error = err.message;
                }).finally(function () {
                    $scope.saving = false;
                });
            };
        }]);
