'use strict';

// Setting é 1:N com user_profile no backend atual.
// O controller suporta:
//   - listar settings do profile (GET /user-profile-setting filtrado client-side)
//   - criar 1 ou vários settings (bulk) usando o formato { user_profile_id, settings: [...] }
//   - editar 1 setting (PUT)
//   - remover (DELETE)
angular.module('yiiApp').controller('UserTabSettingsCtrl',
    ['$scope', 'userProfileSettingService',
        function ($scope, userProfileSettingService) {

            $scope.profile = ($scope.user.profiles || [])[0] || null;
            $scope.settings = [];

            $scope.loading = false;
            $scope.saving = false;
            $scope.message = null;
            $scope.error = null;

            // Modo do form: null = criar (1 ou N) | id = editar aquele setting
            $scope.editingId = null;

            function blankRow() {
                return {
                    theme: 'light',
                    language: 'pt-BR',
                    timezone: 'America/Sao_Paulo',
                    notifications_enabled: true,
                    url: ''
                };
            }

            $scope.rows = [blankRow()];

            // Conceito de arrays (adição de item)
            $scope.addRow = function () {
                $scope.rows.push(blankRow());
            };

            // Conceito de arrays (Remoção de item)
            $scope.removeRow = function (i) {
                if ($scope.rows.length === 1) return;
                $scope.rows.splice(i, 1);
            };

            $scope.startEdit = function (setting) {
                $scope.editingId = setting.id;
                $scope.rows = [{
                    theme: setting.theme,
                    language: setting.language,
                    timezone: setting.timezone,
                    notifications_enabled: !!setting.notifications_enabled,
                    url: setting.url || ''
                }];
                $scope.message = null;
                $scope.error = null;
            };

            $scope.cancelEdit = function () {
                $scope.editingId = null;
                $scope.rows = [blankRow()];
                $scope.error = null;
            };

            function loadSettings() {
                if (!$scope.profile) {
                    $scope.settings = [];
                    return;
                }
                $scope.loading = true;
                return userProfileSettingService.findAllByProfile($scope.profile.id)
                    .then(function (list) {
                        $scope.settings = list || [];
                    })
                    .catch(function (err) {
                        $scope.error = err.message;
                    })
                    .finally(function () {
                        $scope.loading = false;
                    });
            }

            $scope.submit = function () {
                if (!$scope.profile) return;
                $scope.saving = true;
                $scope.message = null;
                $scope.error = null;

                var promise;
                if ($scope.editingId) {
                    // Em edição só temos 1 row, e user_profile_id não muda.
                    promise = userProfileSettingService.update($scope.editingId, $scope.rows[0]);
                } else {
                    // Bulk format: header + lista. O backend aplica user_profile_id em cada item.
                    var payload = {
                        user_profile_id: $scope.profile.id,
                        settings: $scope.rows
                    };
                    promise = userProfileSettingService.create(payload);
                }

                promise.then(function (msg) {
                    $scope.message = msg;
                    $scope.cancelEdit();
                    return loadSettings();
                }).catch(function (err) {
                    $scope.error = err.message;
                }).finally(function () {
                    $scope.saving = false;
                });
            };

            $scope.remove = function (setting) {
                if (!confirm('Remover o setting #' + setting.id + '?')) return;
                $scope.saving = true;
                $scope.message = null;
                $scope.error = null;

                userProfileSettingService.remove(setting.id).then(function () {
                    $scope.message = 'Setting #' + setting.id + ' removido.';
                    if ($scope.editingId === setting.id) $scope.cancelEdit();
                    return loadSettings();
                }).catch(function (err) {
                    $scope.error = err.message;
                }).finally(function () {
                    $scope.saving = false;
                });
            };

            loadSettings();
        }]);
