'use strict';

angular.module('yiiApp').component('userList', {
    templateUrl: 'app/components/user-list/user-list.html',
    controller: ['$state', 'userService', function ($state, userService) {
        var $ctrl = this;

        // Resultado da requisição — shape muda conforme o modo
        $ctrl.users  = [];   // modo normal: lista plana de usuários
        $ctrl.grupos = [];   // modo chunk:  array de arrays (uma tabela por grupo)
        $ctrl.resumo = [];   // modo resumo: [{status, total}]
        $ctrl.modo   = 'normal';

        $ctrl.loading = false;
        $ctrl.error   = null;

        // Filtros — q é client-side; os demais vão como query params pro backend
        $ctrl.filter = {
            q:           '',      // busca por nome/email (client-side)
            status:      '',      // 'ativo' | 'inativo' | ''   → array_filter no back
            ordenacao:   '',      // 'az' | 'za' | ''           → usort no back
            inverter:    false,   //                            → array_reverse no back
            semDominio:  false,   //                            → array_map no back
            pagina:      false,   // ativa paginação            → array_slice no back
            paginaAtual: 1,
            porPagina:   3,
            chunk:       '',      // número N (divide em grupos) → array_chunk no back
            resumo:      false,   //                            → array_reduce no back
            // Novos filtros backend
            busca:       '',      // pesquisa server-side por nome ou email
            limite:      '',      // teto de resultados
            ordenar_por: '',      // campo: id | name | email | created_at | updated_at
            direcao:     'asc',   // 'asc' | 'desc'
            campos:      [],      // projeção de campos (array_keys do objeto retornado)
            coluna:      '',      // extrai array plano de uma coluna
        };

        $ctrl.coluna = [];

        $ctrl.$onInit = function () { $ctrl.load(); };

        // Chamado ao mudar qualquer filtro backend — reseta para página 1
        $ctrl.aplicarFiltros = function () {
            $ctrl.filter.paginaAtual = 1;
            $ctrl.load();
        };

        $ctrl.load = function () {
            $ctrl.loading = true;
            $ctrl.error   = null;

            var params = {};
            if ($ctrl.filter.status)     params.status      = $ctrl.filter.status;
            if ($ctrl.filter.ordenacao)  params.ordenacao   = $ctrl.filter.ordenacao;
            if ($ctrl.filter.inverter)   params.inverter    = 1;
            if ($ctrl.filter.semDominio) params.sem_dominio = 1;
            if ($ctrl.filter.resumo)     params.resumo      = 1;
            if ($ctrl.filter.chunk)      params.chunk       = parseInt($ctrl.filter.chunk, 10);
            if ($ctrl.filter.pagina) {
                params.pagina     = $ctrl.filter.paginaAtual;
                params.por_pagina = $ctrl.filter.porPagina;
            }
            if ($ctrl.filter.busca)                     params.busca       = $ctrl.filter.busca;
            if ($ctrl.filter.limite)                    params.limite      = parseInt($ctrl.filter.limite, 10);
            if ($ctrl.filter.ordenar_por)               { params.ordenar_por = $ctrl.filter.ordenar_por; params.direcao = $ctrl.filter.direcao; }
            if ($ctrl.filter.campos.length)             params['campos[]'] = $ctrl.filter.campos;
            if ($ctrl.filter.coluna)                    params.coluna      = $ctrl.filter.coluna;

            userService.findAll(params).then(function (data) {
                data = data || [];

                if ($ctrl.filter.coluna) {
                    $ctrl.modo   = 'coluna';
                    $ctrl.coluna = data;
                    $ctrl.users  = [];
                    $ctrl.grupos = [];
                    $ctrl.resumo = [];

                } else if ($ctrl.filter.resumo) {
                    $ctrl.modo   = 'resumo';
                    $ctrl.resumo = data;
                    $ctrl.users  = [];
                    $ctrl.grupos = [];
                    $ctrl.coluna = [];

                } else if ($ctrl.filter.chunk && data.length > 0 && Array.isArray(data[0])) {
                    $ctrl.modo   = 'chunk';
                    $ctrl.grupos = data;
                    $ctrl.users  = [];
                    $ctrl.resumo = [];
                    $ctrl.coluna = [];

                } else {
                    $ctrl.modo   = 'normal';
                    $ctrl.users  = data;
                    $ctrl.grupos = [];
                    $ctrl.resumo = [];
                    $ctrl.coluna = [];
                }
            }, function (err) {
                $ctrl.error = err.message;
            }).finally(function () {
                $ctrl.loading = false;
            });
        };

        $ctrl.toggleCampo = function (campo) {
            var idx = $ctrl.filter.campos.indexOf(campo);
            if (idx === -1) { $ctrl.filter.campos.push(campo); }
            else            { $ctrl.filter.campos.splice(idx, 1); }
            $ctrl.aplicarFiltros();
        };

        // Filtro de texto — client-side, mantido como estava
        $ctrl.matchFilter = function (user) {
            var q = ($ctrl.filter.q || '').toLowerCase().trim();
            if (!q) return true;
            return (user.name  || '').toLowerCase().indexOf(q) !== -1
                || (user.email || '').toLowerCase().indexOf(q) !== -1;
        };

        // Paginação (array_slice)
        $ctrl.paginaAnterior = function () {
            if ($ctrl.filter.paginaAtual > 1) {
                $ctrl.filter.paginaAtual--;
                $ctrl.load();
            }
        };
        $ctrl.proximaPagina = function () {
            $ctrl.filter.paginaAtual++;
            $ctrl.load();
        };
        $ctrl.temProximaPagina = function () {
            return $ctrl.users.length >= $ctrl.filter.porPagina;
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
