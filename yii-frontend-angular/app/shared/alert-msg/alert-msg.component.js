'use strict';

// Componente reutilizável de feedback — recebe success e error como bindings (props).
// Uso: <alert-msg success="$ctrl.message" error="$ctrl.error"></alert-msg>
angular.module('yiiApp').component('alertMsg', {
    templateUrl: 'app/shared/alert-msg/alert-msg.html',
    bindings: {
        success: '<',
        error:   '<'
    }
});
