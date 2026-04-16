'use strict';

angular.module('yiiApp').component('tabSettings', {
    templateUrl: 'app/components/tab-settings/tab-settings.html',
    controller: ['userProfileSettingService', 'userEditContext',
        function (userProfileSettingService, userEditContext) {
            var $ctrl = this;

            $ctrl.$onInit = function () {
                $ctrl.profile    = (userEditContext.user.profiles || [])[0] || null;
                $ctrl.settings   = [];
                $ctrl.loading    = false;
                $ctrl.saving     = false;
                $ctrl.message    = null;
                $ctrl.error      = null;
                $ctrl.editingId  = null;
                $ctrl.rows       = [blankRow()];
                loadSettings();
            };

            function blankRow() {
                return {
                    theme:                  'light',
                    language:               'pt-BR',
                    timezone:               'America/Sao_Paulo',
                    notifications_enabled:  true,
                    url:                    ''
                };
            }

            function loadSettings() {
                if (!$ctrl.profile) { $ctrl.settings = []; return; }
                $ctrl.loading = true;
                userProfileSettingService.findAllByProfile($ctrl.profile.id)
                    .then(function (list) { $ctrl.settings = list || []; })
                    .catch(function (err) { $ctrl.error = err.message; })
                    .finally(function ()  { $ctrl.loading = false; });
            }

            $ctrl.addRow    = function ()  { $ctrl.rows.push(blankRow()); };
            $ctrl.removeRow = function (i) {
                if ($ctrl.rows.length === 1) return;
                $ctrl.rows.splice(i, 1);
            };

            $ctrl.startEdit = function (setting) {
                $ctrl.editingId = setting.id;
                $ctrl.rows = [{
                    theme:                 setting.theme,
                    language:              setting.language,
                    timezone:              setting.timezone,
                    notifications_enabled: !!setting.notifications_enabled,
                    url:                   setting.url || ''
                }];
                $ctrl.message = null;
                $ctrl.error   = null;
            };

            $ctrl.cancelEdit = function () {
                $ctrl.editingId = null;
                $ctrl.rows      = [blankRow()];
                $ctrl.error     = null;
            };

            $ctrl.submit = function () {
                if (!$ctrl.profile) return;
                $ctrl.saving  = true;
                $ctrl.message = null;
                $ctrl.error   = null;

                var promise = $ctrl.editingId
                    ? userProfileSettingService.update($ctrl.editingId, $ctrl.rows[0])
                    : userProfileSettingService.create({ user_profile_id: $ctrl.profile.id, settings: $ctrl.rows });

                promise.then(function (msg) {
                    $ctrl.message = msg;
                    $ctrl.cancelEdit();
                    return loadSettings();
                }).catch(function (err) {
                    $ctrl.error = err.message;
                }).finally(function () {
                    $ctrl.saving = false;
                });
            };

            $ctrl.remove = function (setting) {
                if (!confirm('Remover o setting #' + setting.id + '?')) return;
                $ctrl.saving  = true;
                $ctrl.message = null;
                $ctrl.error   = null;

                userProfileSettingService.remove(setting.id).then(function () {
                    $ctrl.message = 'Setting #' + setting.id + ' removido.';
                    if ($ctrl.editingId === setting.id) $ctrl.cancelEdit();
                    return loadSettings();
                }).catch(function (err) {
                    $ctrl.error = err.message;
                }).finally(function () {
                    $ctrl.saving = false;
                });
            };
        }]
});
