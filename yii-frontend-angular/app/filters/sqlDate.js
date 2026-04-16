'use strict';

// Formata datas vindas do backend SQLite. Aceita:
//   - 'YYYY-MM-DD HH:MM:SS' (datetime('now'), assumido UTC)  -> default 'dd/MM/yyyy HH:mm'
//   - 'YYYY-MM-DD'           (date puro, sem timezone)       -> default 'dd/MM/yyyy'
//   - Date instance                                          -> default 'dd/MM/yyyy HH:mm'
// Aceita formato custom como 2º argumento. Falha graciosa: devolve o valor original se não parsear.
angular.module('yiiApp').filter('sqlDate', ['$filter', function ($filter) {
    var dateFilter = $filter('date');

    return function (value, format) {
        if (!value) return '';

        var d, defaultFormat;

        if (value instanceof Date) {
            d = value;
            defaultFormat = 'dd/MM/yyyy HH:mm';
        } else {
            var str = String(value);

            if (/^\d{4}-\d{2}-\d{2}$/.test(str)) {
                // Data pura — interpretamos como local para evitar shift de timezone.
                var parts = str.split('-');
                d = new Date(+parts[0], +parts[1] - 1, +parts[2]);
                defaultFormat = 'dd/MM/yyyy';
            } else {
                // SQLite datetime sem timezone — assumimos UTC (o sufixo Z força isso).
                d = new Date(str.replace(' ', 'T') + 'Z');
                defaultFormat = 'dd/MM/yyyy HH:mm';
            }

            if (isNaN(d.getTime())) return value;
        }

        return dateFilter(d, format || defaultFormat);
    };
}]);
