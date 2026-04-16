'use strict';

angular.module('yiiApp').controller('UserTabConfigsCtrl',
    ['$scope', 'userConfigService',
        function ($scope, userConfigService) {

            $scope.configs = $scope.user.configs || [];

            // editingId === null: form em modo "criar"
            // editingId === <number>: form em modo "atualizar config #<id>"
            $scope.editingId = null;
            $scope.form = { user_id: $scope.userId, key: '', value: '' };

            $scope.saving = false;
            $scope.message = null;
            $scope.error = null;

            function resetForm() {
                $scope.editingId = null;
                $scope.form = { user_id: $scope.userId, key: '', value: '' };
            }

            function refresh() {
                return $scope.reloadUser().then(function (user) {
                    $scope.configs = user.configs || [];
                });
            }

            $scope.startEdit = function (config) {
                $scope.editingId = config.id;
                $scope.form = {
                    user_id: $scope.userId,
                    key: config.key,
                    value: config.value
                };
                $scope.message = null;
                $scope.error = null;
            };

            $scope.cancelEdit = function () {
                resetForm();
                $scope.error = null;
            };

            $scope.submit = function () {
                $scope.saving = true;
                $scope.message = null;
                $scope.error = null;

                var promise = $scope.editingId
                    ? userConfigService.update($scope.editingId, $scope.form)
                    : userConfigService.create($scope.form);

                promise.then(function (msg) {
                    $scope.message = msg;
                    resetForm();
                    return refresh();
                }).catch(function (err) {
                    $scope.error = err.message;
                }).finally(function () {
                    $scope.saving = false;
                });
            };

            $scope.remove = function (config) {
                if (!confirm('Remover a config "' + config.key + '"?')) return;
                $scope.saving = true;
                $scope.message = null;
                $scope.error = null;

                userConfigService.remove(config.id).then(function () {
                    $scope.message = 'Config "' + config.key + '" removida.';
                    if ($scope.editingId === config.id) resetForm();
                    return refresh();
                }).catch(function (err) {
                    $scope.error = err.message;
                }).finally(function () {
                    $scope.saving = false;
                });
            };
        }]);
