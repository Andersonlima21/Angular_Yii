'use strict';

angular
    .module('yiiApp', ['ui.router'])
    .config(['$stateProvider', '$urlRouterProvider', '$locationProvider',
        function ($stateProvider, $urlRouterProvider, $locationProvider) {

            $locationProvider.hashPrefix('');
            $urlRouterProvider.otherwise('/users');

            $stateProvider
                .state('users', {
                    url: '/users',
                    templateUrl: 'app/views/user-list.html',
                    controller: 'UserListCtrl'
                })
                .state('newUser', {
                    url: '/users/new',
                    templateUrl: 'app/views/user-create.html',
                    controller: 'UserCreateCtrl'
                })
                // Parent state que carrega o user e expõe via $scope.user para os filhos.
                .state('editUser', {
                    url: '/users/{id:[0-9]+}/edit',
                    templateUrl: 'app/views/user-edit.html',
                    controller: 'UserEditCtrl',
                    resolve: {
                        userData: ['$stateParams', 'userService',
                            function ($stateParams, userService) {
                                return userService.findById($stateParams.id);
                            }]
                    }
                })
                .state('editUser.info', {
                    url: '/info',
                    templateUrl: 'app/views/tab-info.html',
                    controller: 'UserTabInfoCtrl'
                })
                .state('editUser.configs', {
                    url: '/configs',
                    templateUrl: 'app/views/tab-configs.html',
                    controller: 'UserTabConfigsCtrl'
                })
                .state('editUser.profiles', {
                    url: '/profiles',
                    templateUrl: 'app/views/tab-profiles.html',
                    controller: 'UserTabProfilesCtrl'
                })
;
        }])
    // Quando entrar em editUser sem aba, redireciona para info por padrão.
    .run(['$transitions', '$state', function ($transitions, $state) {
        $transitions.onSuccess({ to: 'editUser' }, function (trans) {
            $state.go('editUser.info', trans.params());
        });
    }]);
