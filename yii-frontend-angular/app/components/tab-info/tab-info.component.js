'use strict';

angular.module('yiiApp').component('tabInfo', {
    templateUrl: 'app/components/tab-info/tab-info.html',
    controller: ['userService', 'userEditContext', function (userService, userEditContext) {
        var $ctrl = this;

        $ctrl.$onInit = function () {
            $ctrl.user = userEditContext.user;
            $ctrl.form = {
                name:  userEditContext.user.name,
                email: userEditContext.user.email
            };
            $ctrl.saving  = false;
            $ctrl.message = null;
            $ctrl.error   = null;
        };

        $ctrl.save = function () {
            $ctrl.saving  = true;
            $ctrl.message = null;
            $ctrl.error   = null;

            userService.update(userEditContext.userId, $ctrl.form).then(function (msg) {
                $ctrl.message = msg;
                return userEditContext.reload();
            }).then(function () {
                $ctrl.user = userEditContext.user;
            }).catch(function (err) {
                $ctrl.error = err.message;
            }).finally(function () {
                $ctrl.saving = false;
            });
        };
    }]
});
