'use strict';

angular.module('yiiApp').component('userList', {
    templateUrl: 'app/components/user-list/user-list.html',
    controller: ['$state', 'userService', function ($state, userService) {
        var $ctrl = this;

        $ctrl.users   = [];
        $ctrl.filter  = { q: '', ativo: '' };
        $ctrl.loading = false;
        $ctrl.error   = null;

        $ctrl.$onInit = function () { $ctrl.load(); };

        $ctrl.load = function () {
            $ctrl.loading = true;
            $ctrl.error   = null;
            userService.findAll().then(function (data) {
                $ctrl.users = data || [];
            }, function (err) {
                $ctrl.error = err.message;
            }).finally(function () {
                $ctrl.loading = false;
            });
        };

        $ctrl.matchFilter = function (user) {
            var q = ($ctrl.filter.q || '').toLowerCase().trim();
            if (q && (user.name  || '').toLowerCase().indexOf(q) === -1
                  && (user.email || '').toLowerCase().indexOf(q) === -1) return false;
            if ($ctrl.filter.ativo === 'sim' && !user.is_active) return false;
            if ($ctrl.filter.ativo === 'nao' &&  user.is_active) return false;
            return true;
        };

        $ctrl.edit   = function (id) { $state.go('editUser.info', { id: id }); };
        $ctrl.create = function ()   { $state.go('newUser'); };

        $ctrl.toggleActive = function (user) {
            var acao = user.is_active ? 'inativar' : 'ativar';
            if (!confirm('Deseja ' + acao + ' o usuário "' + user.name + '"?')) return;
            userService.toggleActive(user.id).then(function (data) {
                user.is_active = data.is_active;
            }, function (err) { alert(err.message); });
        };

        $ctrl.remove = function (user) {
            if (!confirm('Remover o usuário "' + user.name + '"?')) return;
            userService.remove(user.id).then(function () {
                $ctrl.load();
            }, function (err) { alert(err.message); });
        };
    }]
});
