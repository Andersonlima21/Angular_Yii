angular.module('yiiApp').directive('bsTooltip', function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            var tip = new bootstrap.Tooltip(element[0], {
                title: attrs.bsTooltip,
                placement: attrs.tooltipPlacement || 'top',
                trigger: 'hover'
            });
            scope.$on('$destroy', function () {
                tip.dispose();
            });
        }
    };
});
