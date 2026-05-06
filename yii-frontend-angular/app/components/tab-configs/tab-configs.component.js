'use strict';

angular.module('yiiApp').component('tabConfigs', {
    templateUrl: 'app/components/tab-configs/tab-configs.html',
    controller: ['userConfigService', 'userEditContext', function (userConfigService, userEditContext) {
        var $ctrl = this;

        $ctrl.$onInit = function () {
            $ctrl.configs = userEditContext.user.configs || [];
            $ctrl.editingId = null;
            $ctrl.saving = false;
            $ctrl.message = null;
            $ctrl.error = null;
            resetForm();
        };

        function resetForm() {
            $ctrl.editingId = null;
            $ctrl.form = { user_id: userEditContext.userId, key: '', value: '' };
        }

        function refresh() {
            return userEditContext.reload().then(function (user) {
                $ctrl.configs = user.configs || [];
            });
        }

        $ctrl.startEdit = function (config) {
            $ctrl.editingId = config.id;
            $ctrl.form = { user_id: userEditContext.userId, key: config.key, value: config.value };
            $ctrl.message = null;
            $ctrl.error = null;
        };

        $ctrl.cancelEdit = function () {
            resetForm();
            $ctrl.error = null;
        };

        $ctrl.submit = function () {
            $ctrl.saving = true;
            $ctrl.message = null;
            $ctrl.error = null;

            var promise = $ctrl.editingId
                ? userConfigService.update($ctrl.editingId, $ctrl.form)
                : userConfigService.create($ctrl.form);

            promise.then(function (msg) {
                $ctrl.message = msg;
                resetForm();
                return refresh();
            }).catch(function (err) {
                $ctrl.error = err.message;
            }).finally(function () {
                $ctrl.saving = false;
            });
        };

        $ctrl.remove = function (config) {
            if (!confirm('Remover a config "' + config.key + '"?')) return;
            $ctrl.saving = true;
            $ctrl.message = null;
            $ctrl.error = null;

            userConfigService.remove(config.id).then(function () {
                $ctrl.message = 'Config "' + config.key + '" removida.';
                if ($ctrl.editingId === config.id) resetForm();
                return refresh();
            }).catch(function (err) {
                $ctrl.error = err.message;
            }).finally(function () {
                $ctrl.saving = false;
            });
        };
    }]
});
