'use strict';

// Aplica máscara (XX) XXXXX-XXXX enquanto o usuário digita e valida
// se o número está completo (11 dígitos). Campo vazio é considerado válido.
angular.module('yiiApp').directive('phoneMask', function () {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, element, attrs, ngModel) {

            function applyMask(raw) {
                var d = (raw || '').replace(/\D/g, '').slice(0, 11);
                if (d.length === 0)  return '';
                if (d.length <= 2)   return '(' + d;
                if (d.length <= 7)   return '(' + d.slice(0, 2) + ') ' + d.slice(2);
                return '(' + d.slice(0, 2) + ') ' + d.slice(2, 7) + '-' + d.slice(7);
            }

            // view → model: aplica máscara e valida completude
            ngModel.$parsers.unshift(function (viewValue) {
                var masked = applyMask(viewValue);
                if (masked !== viewValue) {
                    ngModel.$setViewValue(masked);
                    ngModel.$render();
                }
                var digits = masked.replace(/\D/g, '');
                ngModel.$setValidity('phone', digits.length === 0 || digits.length === 11);
                return masked;
            });

            // model → view: formata ao popular via $scope
            ngModel.$formatters.push(applyMask);
        }
    };
});
