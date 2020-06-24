<?php

return [
    'dbname'   => getenv('DB_NAME') ?: 'beejee_test',
    'user'     => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?:'r56t',
    'host'     => getenv('DB_HOST') ?: 'localhost',
    'port'     => getenv('DB_PORT') ?: 3307,
    'charset'  => 'utf8',
    'driver'   => 'pdo_mysql',
];

//return [
//    'dbname'   => 'beejee_test',
//    'user'     => 'doadmin',
//    'password' => 'd3iggh62toojdayt',
//    'host'     => 'db-mysql-fra1-one-do-user-7647016-0.a.db.ondigitalocean.com',
//    'port'     => 25060,
//    'charset'  => 'utf8',
//    'driver'   => 'pdo_mysql',
//];
