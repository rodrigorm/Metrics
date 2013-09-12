<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/library/data.php';

use \Slim\Slim;

$db = db();
$app = new Slim();

$app->get('/data', function () use ($db) {
    echo json_encode(fetch($db, $_GET['from'], $_GET['until']));
});

$app->run();
