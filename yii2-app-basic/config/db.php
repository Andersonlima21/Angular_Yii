<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'sqlite:' . dirname(__DIR__) . '/runtime/database.sqlite',
    'charset' => 'utf8',

    // PDO por padrão devolve TODOS os valores como string quando lê do SQLite.
    // Esses dois atributos desligam essa "stringificação" e mantêm os tipos
    // originais do banco (int continua int, float continua float).
    'attributes' => [
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES  => false,
    ],
];
