'use strict';

angular
    .module('yiiApp', ['ui.router'])
    .config(['$stateProvider', '$urlRouterProvider', '$locationProvider',
        function ($stateProvider, $urlRouterProvider, $locationProvider) {

            $locationProvider.hashPrefix('');
            $urlRouterProvider.otherwise('/users');

            $stateProvider
                .state('users', {
                    url:       '/users',
                    component: 'userList'
                })
                .state('newUser', {
                    url:       '/users/new',
                    component: 'userCreate'
                })
                // Parent state — resolve carrega o usuário e injeta como binding no componente.
                // O componente user-edit publica esses dados no userEditContext para as tabs filhas.
                .state('editUser', {
                    url:       '/users/{id:[0-9]+}/edit',
                    component: 'userEdit',
                    resolve: {
                        userData: ['$stateParams', 'userService',
                            function ($stateParams, userService) {
                                return userService.findById($stateParams.id);
                            }]
                    }
                })
                .state('editUser.info', {
                    url:       '/info',
                    component: 'tabInfo'
                })
                .state('editUser.configs', {
                    url:       '/configs',
                    component: 'tabConfigs'
                })
                .state('editUser.profiles', {
                    url:       '/profiles',
                    component: 'tabProfiles'
                })
;        }])
    // Quando entrar em editUser sem aba, redireciona para info por padrão.
    .run(['$transitions', '$state', function ($transitions, $state) {
        $transitions.onSuccess({ to: 'editUser' }, function (trans) {
            $state.go('editUser.info', trans.params());
        });
    }]);
