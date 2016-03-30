<?php

// enable the debug mode
$app['debug'] = true;

$app['searchResult.perPage'] = 5;

// DB
$app['db.options'] = array(
		'driver'   => 'pdo_mysql',
		'dbname' => 'eatme',
		'user' => '',
		'password' => '',
		'host' => 'localhost',
		'prefix' => 'eatme_',
);

$app['monolog.options'] = array(
		'monolog.level' => 'DEBUG',
		'monolog.logfile' => __DIR__. '/../../var/logs/debug.log',
);
