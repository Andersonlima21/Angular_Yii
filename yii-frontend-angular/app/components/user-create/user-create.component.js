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

            userService.create($ctrl.form).then(function (data) {
                $state.go('editUser.info', { id: data.id });
            }).catch(function (err) {
                $ctrl.error = err.message;
            }).finally(function () {
                $ctrl.saving = false;
            });
        };
    }]
});
