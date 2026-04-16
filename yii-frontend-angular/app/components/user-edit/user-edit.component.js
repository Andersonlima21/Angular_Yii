'use strict';

// Componente pai da tela de edição — recebe userData via resolve do state (como uma prop).
// Popula o userEditContext para que os componentes-filhos (tabs) possam acessar o usuário
// e solicitar um reload, sem precisar de herança de $scope.
angular.module('yiiApp').component('userEdit', {
    templateUrl: 'app/components/user-edit/user-edit.html',
    bindings: {
        userData: '<'   // injetado automaticamente pelo resolve do state
    },
    controller: ['$state', '$stateParams', 'userService', 'userEditContext',
        function ($state, $stateParams, userService, userEditContext) {
            var $ctrl = this;

            $ctrl.$onInit = function () {
                $ctrl.user   = $ctrl.userData;
                $ctrl.userId = parseInt($stateParams.id, 10);

                // Publica no contexto — equivale a um Provider do React Context
                userEditContext.user   = $ctrl.user;
                userEditContext.userId = $ctrl.userId;
                userEditContext.reload = $ctrl.reloadUser;
            };

            $ctrl.reloadUser = function () {
                return userService.findById($ctrl.userId).then(function (data) {
                    $ctrl.user             = data;
                    userEditContext.user   = data;
                    return data;
                });
            };

            $ctrl.backToList = function () { $state.go('users'); };
        }]
});
