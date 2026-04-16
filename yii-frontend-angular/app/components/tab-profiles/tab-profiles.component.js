'use strict';

angular.module('yiiApp').component('tabProfiles', {
    templateUrl: 'app/components/tab-profiles/tab-profiles.html',
    controller: ['userProfileService', 'userProfileSettingService', 'userEditContext',
        function (userProfileService, userProfileSettingService, userEditContext) {
            var $ctrl = this;

            // ── Profile ──────────────────────────────────────────────────────────

            $ctrl.$onInit = function () {
                $ctrl.profile        = (userEditContext.user.profiles || [])[0] || null;
                $ctrl.saving         = false;
                $ctrl.profileMessage = null;
                $ctrl.profileError   = null;
                $ctrl.form           = buildForm($ctrl.profile);

                $ctrl.settings       = [];
                $ctrl.loadingSettings = false;
                $ctrl.settingMessage = null;
                $ctrl.settingError   = null;
                $ctrl.editingId      = null;
                $ctrl.rows           = [blankRow()];

                loadSettings();
            };

            function toIsoDate(value) {
                if (!value) return null;
                if (value instanceof Date) return value.toISOString().slice(0, 10);
                return value;
            }

            function fromIsoDate(value) {
                if (!value) return null;
                if (value instanceof Date) return value;
                var parts = String(value).split('-');
                if (parts.length !== 3) return null;
                return new Date(+parts[0], +parts[1] - 1, +parts[2]);
            }

            function buildForm(profile) {
                return {
                    user_id:    userEditContext.userId,
                    phone:      profile ? profile.phone      : '',
                    birth_date: profile ? fromIsoDate(profile.birth_date) : null,
                    bio:        profile ? profile.bio        : '',
                    avatar_url: profile ? profile.avatar_url : ''
                };
            }

            function refresh(skipSettings) {
                return userEditContext.reload().then(function (user) {
                    $ctrl.profile = (user.profiles || [])[0] || null;
                    $ctrl.form    = buildForm($ctrl.profile);
                    if (!skipSettings) loadSettings();
                });
            }

            $ctrl.submit = function () {
                $ctrl.saving         = true;
                $ctrl.profileMessage = null;
                $ctrl.profileError   = null;

                var isCreate = !$ctrl.profile;
                var payload  = angular.extend({}, $ctrl.form, {
                    birth_date: toIsoDate($ctrl.form.birth_date)
                });

                var promise = isCreate
                    ? userProfileService.create(payload)
                    : userProfileService.update($ctrl.profile.id, payload);

                promise.then(function (msg) {
                    $ctrl.profileMessage = msg;
                    return refresh(isCreate);
                }).catch(function (err) {
                    $ctrl.profileError = err.message;
                }).finally(function () {
                    $ctrl.saving = false;
                });
            };

            $ctrl.removeProfile = function () {
                if (!$ctrl.profile) return;
                if (!confirm('Remover o profile? Os settings vinculados também serão removidos.')) return;

                $ctrl.saving         = true;
                $ctrl.profileMessage = null;
                $ctrl.profileError   = null;

                userProfileService.remove($ctrl.profile.id).then(function () {
                    $ctrl.profileMessage = 'Profile removido.';
                    $ctrl.settings       = [];
                    return refresh(true);
                }).catch(function (err) {
                    $ctrl.profileError = err.message;
                }).finally(function () {
                    $ctrl.saving = false;
                });
            };

            // ── Settings ─────────────────────────────────────────────────────────

            function blankRow() {
                return { platform: 'alura', stack: 'php', certificate_url: '' };
            }

            function loadSettings() {
                if (!$ctrl.profile) { $ctrl.settings = []; return; }
                $ctrl.loadingSettings = true;
                userProfileSettingService.findAllByProfile($ctrl.profile.id)
                    .then(function (list) { $ctrl.settings = list || []; })
                    .catch(function (err) { $ctrl.settingError = err.message; })
                    .finally(function ()  { $ctrl.loadingSettings = false; });
            }

            $ctrl.addRow    = function ()  { $ctrl.rows.push(blankRow()); };
            $ctrl.removeRow = function (i) {
                if ($ctrl.rows.length === 1) return;
                $ctrl.rows.splice(i, 1);
            };

            $ctrl.startEdit = function (setting) {
                $ctrl.editingId      = setting.id;
                $ctrl.rows           = [{ platform: setting.platform, stack: setting.stack, certificate_url: setting.certificate_url || '' }];
                $ctrl.settingMessage = null;
                $ctrl.settingError   = null;
            };

            $ctrl.cancelEdit = function () {
                $ctrl.editingId    = null;
                $ctrl.rows         = [blankRow()];
                $ctrl.settingError = null;
            };

            $ctrl.submitSetting = function () {
                if (!$ctrl.profile) return;
                $ctrl.saving         = true;
                $ctrl.settingMessage = null;
                $ctrl.settingError   = null;

                var promise = $ctrl.editingId
                    ? userProfileSettingService.update($ctrl.editingId, $ctrl.rows[0])
                    : userProfileSettingService.create({ user_profile_id: $ctrl.profile.id, settings: $ctrl.rows });

                promise.then(function (msg) {
                    $ctrl.settingMessage = msg;
                    $ctrl.cancelEdit();
                    loadSettings();
                }).catch(function (err) {
                    $ctrl.settingError = err.message;
                }).finally(function () {
                    $ctrl.saving = false;
                });
            };

            $ctrl.removeSetting = function (setting) {
                if (!confirm('Remover o setting #' + setting.id + '?')) return;
                $ctrl.saving         = true;
                $ctrl.settingMessage = null;
                $ctrl.settingError   = null;

                userProfileSettingService.remove(setting.id).then(function () {
                    $ctrl.settingMessage = 'Setting #' + setting.id + ' removido.';
                    if ($ctrl.editingId === setting.id) $ctrl.cancelEdit();
                    loadSettings();
                }).catch(function (err) {
                    $ctrl.settingError = err.message;
                }).finally(function () {
                    $ctrl.saving = false;
                });
            };

            $ctrl.isValidUrl = function (value) {
                if (!value) return true;
                try { return !!new URL(value); } catch (e) { return false; }
            };
        }]
});
