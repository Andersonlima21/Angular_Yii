<?php

$db = require __DIR__ . '/db.php';
// banco separado para não tocar nos dados de desenvolvimento
$db['dsn'] = 'sqlite:' . dirname(__DIR__) . '/runtime/test_database.sqlite';

return $db;
