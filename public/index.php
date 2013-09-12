<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/library/data.php';
require_once dirname(__DIR__) . '/library/api.php';

use \Slim\Slim;

$db = db();
$app = new Slim();

$app->get('/data', function () use ($db) {
    $start = microtime(true);
    $result = fetch($db, $_GET['target'], $_GET['from'], $_GET['until']);
    metric_collect('metrics.get.elapsed', (microtime(true) - $start) * 1000);
    echo json_encode($result);
});

$app->run();
