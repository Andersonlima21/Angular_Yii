'use strict';

angular.module('yiiApp').component('userCreate', {
    templateUrl: 'app/components/user-create/user-create.html',
    controller: ['$state', 'userService', function ($state, userService) {
        var $ctrl = this;

        $ctrl.form   = { name: '', email: '' };
        $ctrl.saving = false;
        $ctrl.error  = null;

        $ctrl.cancel = function () { $state.go('users'); };

        $ctrl.submit = function () {
            $ctrl.saving = true;
            $ctrl.error  = null;

            userService.create($ctrl.form).then(function () {
                // O backend devolve apenas uma string de sucesso, sem o id.
                // Localizamos o usuário pelo email (unique) para redirecionar para edição.
                return userService.findAll();
            }).then(function (list) {
                var created = (list || []).filter(function (u) {
                    return u.email === $ctrl.form.email;
                })[0];
                if (!created) {
                    throw new Error('Usuário criado, mas não foi possível localizá-lo na listagem.');
                }
                $state.go('editUser.info', { id: created.id });
            }).catch(function (err) {
                $ctrl.error = err.message;
            }).finally(function () {
                $ctrl.saving = false;
            });
        };
    }]
});
