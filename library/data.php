<?php

function db()
{
    $db = new PDO('mysql:dbname=benchmark;host=127.0.0.1', 'root', 'root', array(PDO::ATTR_PERSISTENT => false));
    $db->exec("set time_zone = '-0300';");
    return $db;
}

function fetch($db, $from, $until)
{
    $from = new DateTime($from);
    $until = new DateTime($until);

    $current = new DateTime($from->format('Y-m-01 00:00:00'));
    $result = array();

    while (true) {
        $last = new DateTime($current->format('Y-m-01 23:59:59'));
        $last->modify('next month')->modify('previous day');

        $result = combine($result, select_range($db, $current->format('Y-m-d 00:00:00'), $last->format('Y-m-d 23:59:59')));

        $current->modify('next month');

        if ($current->format('Ym') > $until->format('Ym')) {
            break;
        }
    }

    return filter($result, $from, $until);
}

function insert($db, $target, $value, $timestamp)
{
    $metricId = metric($db, $target);
    $created = new DateTime('@' . $timestamp);

    $insert = $db->prepare("INSERT INTO data (metric_id, value, created) VALUE (:metric_id, :value, :created)");
    $insert->execute(array(
        ':metric_id' => $metricId,
        ':value' => $value,
        ':created' => $created->format('Y-m-d H:i:s')
    ));
}

function metric($db, $name)
{
    $select = $db->prepare("SELECT id FROM metrics WHERE name = :name");

    $select->execute(array('name' => $name));
    $result = $select->fetch();

    if (empty($result['id'])) {
        $insert = $db->prepare("INSERT INTO metrics (name) VALUE (:name)");
        $insert->execute(array(':name' => $name));

        $select->execute(array(':name' => $name));
        $result = $select->fetch();
    }

    return $result['id'];
}

function filter($serie, $from, $until) {
    $result = array();

    foreach ($serie as $item) {
        $time = strtotime($item['time']);
        if ($from->getTimestamp() <= $time && $time <= $until->getTimestamp()) {
            $result[] = $item;
        }
    }

    return $result;
}

/**
 * SELECT
 * FLOOR(UNIX_TIMESTAMP(created) / 300) * 300 AS time, SUM(value)
 * FROM data
 * WHERE created BETWEEN '2012-12-12 00:00:00' AND '2013-03-12 23:59:59'
 * GROUP BY time;
 */
function select_range($db, $from, $until)
{
    return combine(
        select($db, $from, $until, ''),
        select($db, $from, $until, '_5min'),
        select($db, $from, $until, '_60min')
    );
}

function select($db, $from, $until, $interval = '', $group = 60)
{
    $select = $db->prepare("
        SELECT FROM_UNIXTIME(FLOOR(UNIX_TIMESTAMP(created) / :group) * :group) AS time, SUM(value) value
        FROM data${interval}
        WHERE created BETWEEN :from AND :until
        GROUP BY time
    ");

    $select->execute(array(
        ':group' => $group,
        ':from' => $from,
        ':until' => $until
    ));

    return $select->fetchAll();
}

function combine(/* $series... */) {
    $series = func_get_args();

    $order = array();
    $result = array();

    foreach ($series as $serie) {
        foreach ($serie as $item) {
            $order[$item['time']] = $item['time'];

            if (array_key_exists($item['time'], $result)) {
                $result[$item['time']]['value'] += $item['value'];
            } else {
                $result[$item['time']] = $item;
            }
        }
    }

    array_multisort($order, $result, SORT_ASC);
    return $result;
}
