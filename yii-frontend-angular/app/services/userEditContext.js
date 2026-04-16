'use strict';

// Análogo ao React Context — compartilha o usuário em edição entre o componente
// pai (user-edit) e os componentes filhos (tab-info, tab-configs, tab-profiles, tab-settings).
// O user-edit popula este serviço no $onInit; as tabs apenas lêem.
angular.module('yiiApp').service('userEditContext', function () {
    this.user   = null;
    this.userId = null;
    this.reload = function () {};   // sobrescrito pelo user-edit ao inicializar
});
