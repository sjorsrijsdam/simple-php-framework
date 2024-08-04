<?php

require_once __DIR__ . '/../Framework/Container.php';

$container = new \Framework\Container();

$container->load(
    services: [
        'App' => __DIR__ . '/../Src',
        'Framework' => __DIR__ . '/../Framework',
    ],
    bind: [
        '$usersDbPath' => __DIR__ . '/../_data/users.sq3',
    ]
);

$app = $container->get(\Framework\App::class);
$app->run();
