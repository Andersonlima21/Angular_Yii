'use strict';

// Wrapper sobre /user-api. O backend devolve { success, type, data|message }.
// Aqui devolvemos somente o `data` em caso de sucesso e propagamos o `message` em caso de erro.
angular.module('yiiApp').factory('userService', ['$http', 'API_BASE_URL',
    function ($http, API_BASE_URL) {

        var url = API_BASE_URL + '/user-api';

        function unwrap(resp) {
            return resp.data && resp.data.data;
        }

        function rejectWithMessage(err) {
            var msg = (err && err.data && err.data.message) || 'Erro desconhecido na requisição.';
            return Promise.reject(new Error(msg));
        }

        return {
            findAll: function (params) {
                return $http.get(url, { params: params || {} }).then(unwrap, rejectWithMessage);
            },
            findById: function (id) {
                return $http.get(url + '/' + id).then(unwrap, rejectWithMessage);
            },
            create: function (body) {
                return $http.post(url, body).then(unwrap, rejectWithMessage);
            },
            update: function (id, body) {
                return $http.put(url + '/' + id, body).then(unwrap, rejectWithMessage);
            },
            remove: function (id) {
                return $http.delete(url + '/' + id).then(function () { return true; }, rejectWithMessage);
            },
            toggleActive: function (id) {
                return $http.patch(url + '/' + id + '/toggle-active').then(unwrap, rejectWithMessage);
            }
        };
    }]);
