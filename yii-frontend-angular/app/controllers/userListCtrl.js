'use strict';

angular.module('yiiApp').controller('UserListCtrl',
    ['$scope', '$state', 'userService',
        function ($scope, $state, userService) {

            $scope.users = [];
            $scope.filter = { q: '', ativo: '' };
            $scope.loading = false;
            $scope.error = null;

            $scope.load = function () {
                $scope.loading = true;
                $scope.error = null;
                userService.findAll().then(function (data) {
                    $scope.users = data || [];
                }, function (err) {
                    $scope.error = err.message;
                }).finally(function () {
                    $scope.loading = false;
                });
            };

            // Predicate usado pelo filter do AngularJS na repeat.
            $scope.matchFilter = function (user) {
                var q = ($scope.filter.q || '').toLowerCase().trim();
                if (q && (user.name || '').toLowerCase().indexOf(q) === -1
                      && (user.email || '').toLowerCase().indexOf(q) === -1) {
                    return false;
                }
                if ($scope.filter.ativo === 'sim' && !user.is_active) return false;
                if ($scope.filter.ativo === 'nao' && user.is_active) return false;
                return true;
            };

            $scope.edit = function (id) { $state.go('editUser.info', { id: id }); };
            $scope.create = function () { $state.go('newUser'); };

            $scope.toggleActive = function (user) {
                var acao = user.is_active ? 'inativar' : 'ativar';
                if (!confirm('Deseja ' + acao + ' o usuário "' + user.name + '"?')) return;
                userService.toggleActive(user.id).then(function (data) {
                    user.is_active = data.is_active;
                }, function (err) { alert(err.message); });
            };

            $scope.remove = function (user) {
                if (!confirm('Remover o usuário "' + user.name + '"?')) return;
                userService.remove(user.id).then(function () {
                    $scope.load();
                }, function (err) { alert(err.message); });
            };

            $scope.load();
        }]);
