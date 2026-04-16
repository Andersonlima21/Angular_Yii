'use strict';

angular.module('yiiApp').controller('UserTabProfilesCtrl',
    ['$scope', 'userProfileService', 'userProfileSettingService',
        function ($scope, userProfileService, userProfileSettingService) {

            // ── Profile ────────────────────────────────────────────────────────────
            $scope.profile = ($scope.user.profiles || [])[0] || null;
            $scope.saving = false;
            $scope.profileMessage = null;
            $scope.profileError = null;

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
                    user_id: $scope.userId,
                    phone: profile ? profile.phone : '',
                    birth_date: profile ? fromIsoDate(profile.birth_date) : null,
                    bio: profile ? profile.bio : '',
                    avatar_url: profile ? profile.avatar_url : ''
                };
            }

            $scope.form = buildForm($scope.profile);

            function refresh(skipSettings) {
                return $scope.reloadUser().then(function (user) {
                    $scope.profile = (user.profiles || [])[0] || null;
                    $scope.form = buildForm($scope.profile);
                    if (!skipSettings) loadSettings();
                });
            }

            $scope.submit = function () {
                $scope.saving = true;
                $scope.profileMessage = null;
                $scope.profileError = null;

                var isCreate = !$scope.profile;
                var payload = angular.extend({}, $scope.form, {
                    birth_date: toIsoDate($scope.form.birth_date)
                });

                var promise = isCreate
                    ? userProfileService.create(payload)
                    : userProfileService.update($scope.profile.id, payload);

                promise.then(function (msg) {
                    $scope.profileMessage = msg;
                    return refresh(isCreate);
                }).catch(function (err) {
                    $scope.profileError = err.message;
                }).finally(function () {
                    $scope.saving = false;
                });
            };

            $scope.removeProfile = function () {
                if (!$scope.profile) return;
                if (!confirm('Remover o profile? Os settings vinculados também serão removidos.')) return;

                $scope.saving = true;
                $scope.profileMessage = null;
                $scope.profileError = null;

                userProfileService.remove($scope.profile.id).then(function () {
                    $scope.profileMessage = 'Profile removido.';
                    $scope.settings = [];
                    return refresh(true);
                }).catch(function (err) {
                    $scope.profileError = err.message;
                }).finally(function () {
                    $scope.saving = false;
                });
            };

            // ── Settings ───────────────────────────────────────────────────────────
            $scope.settings = [];
            $scope.loadingSettings = false;
            $scope.settingMessage = null;
            $scope.settingError = null;
            $scope.editingId = null;

            function blankRow() {
                return {
                    platform: 'alura',
                    stack: 'php',
                    certificate_url: ''
                };
            }

            $scope.rows = [blankRow()];

            $scope.addRow = function () {
                $scope.rows.push(blankRow());
            };

            $scope.removeRow = function (i) {
                if ($scope.rows.length === 1) return;
                $scope.rows.splice(i, 1);
            };

            $scope.startEdit = function (setting) {
                $scope.editingId = setting.id;
                $scope.rows = [{
                    platform: setting.platform,
                    stack: setting.stack,
                    certificate_url: setting.certificate_url || ''
                }];
                $scope.settingMessage = null;
                $scope.settingError = null;
            };

            $scope.cancelEdit = function () {
                $scope.editingId = null;
                $scope.rows = [blankRow()];
                $scope.settingError = null;
            };

            function loadSettings() {
                if (!$scope.profile) {
                    $scope.settings = [];
                    return;
                }
                $scope.loadingSettings = true;
                userProfileSettingService.findAllByProfile($scope.profile.id)
                    .then(function (list) {
                        $scope.settings = list || [];
                    })
                    .catch(function (err) {
                        $scope.settingError = err.message;
                    })
                    .finally(function () {
                        $scope.loadingSettings = false;
                    });
            }

            $scope.submitSetting = function () {
                if (!$scope.profile) return;
                $scope.saving = true;
                $scope.settingMessage = null;
                $scope.settingError = null;

                var promise;
                if ($scope.editingId) {
                    promise = userProfileSettingService.update($scope.editingId, $scope.rows[0]);
                } else {
                    promise = userProfileSettingService.create({
                        user_profile_id: $scope.profile.id,
                        settings: $scope.rows
                    });
                }

                promise.then(function (msg) {
                    $scope.settingMessage = msg;
                    $scope.cancelEdit();
                    loadSettings();
                }).catch(function (err) {
                    $scope.settingError = err.message;
                }).finally(function () {
                    $scope.saving = false;
                });
            };

            $scope.removeSetting = function (setting) {
                if (!confirm('Remover o setting #' + setting.id + '?')) return;
                $scope.saving = true;
                $scope.settingMessage = null;
                $scope.settingError = null;

                userProfileSettingService.remove(setting.id).then(function () {
                    $scope.settingMessage = 'Setting #' + setting.id + ' removido.';
                    if ($scope.editingId === setting.id) $scope.cancelEdit();
                    loadSettings();
                }).catch(function (err) {
                    $scope.settingError = err.message;
                }).finally(function () {
                    $scope.saving = false;
                });
            };

            // Usado nas rows de settings (ng-repeat) onde não dá pra nomear inputs individualmente.
            $scope.isValidUrl = function (value) {
                if (!value) return true;
                try { return !!new URL(value); } catch (e) { return false; }
            };

            loadSettings();
        }]);
