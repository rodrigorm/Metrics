<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/library/data.php';

use \Slim\Slim;

$db = new PDO('mysql:dbname=benchmark;host=127.0.0.1', 'root', 'root', array(PDO::ATTR_PERSISTENT => false));
$db->exec("set time_zone = '-0300';");
$app = new Slim();

$app->get('/data', function () use ($db) {
    echo json_encode(fetch($db, $_GET['from'], $_GET['until']));
});

$app->run();
