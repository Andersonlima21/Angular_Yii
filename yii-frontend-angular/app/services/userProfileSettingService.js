'use strict';

// Wrapper sobre /user-profile-setting — backend tem CRUD completo + bulk create.
// O endpoint não filtra por profile, então findAllByProfile filtra client-side.
angular.module('yiiApp').factory('userProfileSettingService', ['$http', 'API_BASE_URL',
    function ($http, API_BASE_URL) {

        var url = API_BASE_URL + '/user-profile-setting';

        function unwrap(resp) { return resp.data && resp.data.data; }
        function rejectWithMessage(err) {
            var msg = (err && err.data && err.data.message) || 'Erro desconhecido na requisição.';
            return Promise.reject(new Error(msg));
        }

        return {
            findAll: function () {
                return $http.get(url).then(unwrap, rejectWithMessage);
            },
            findAllByProfile: function (profileId) {
                return $http.get(url).then(function (resp) {
                    var all = (resp.data && resp.data.data) || [];
                    return all.filter(function (s) { return s.user_profile_id === profileId; });
                }, function (err) {
                    // O backend devolve 400 com "Nenhum setting encontrado!" quando a tabela está vazia.
                    // Tratamos como lista vazia, não como erro.
                    var msg = err && err.data && err.data.message;
                    if (msg && /nenhum setting/i.test(msg)) return [];
                    return rejectWithMessage(err);
                });
            },
            findById: function (id) {
                return $http.get(url + '/' + id).then(unwrap, rejectWithMessage);
            },
            // Aceita objeto único OU lista (para bulk). O backend reconhece os dois formatos.
            create: function (body) {
                return $http.post(url, body).then(unwrap, rejectWithMessage);
            },
            update: function (id, body) {
                return $http.put(url + '/' + id, body).then(unwrap, rejectWithMessage);
            },
            remove: function (id) {
                return $http.delete(url + '/' + id).then(function () { return true; }, rejectWithMessage);
            }
        };
    }]);
