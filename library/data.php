<?php

function db()
{
    $db = new PDO('mysql:dbname=benchmark;host=127.0.0.1', 'root', 'root', array(PDO::ATTR_PERSISTENT => false));
    $db->exec("set time_zone = '-0300';");
    return $db;
}

function fetch($db, $target, $from, $until)
{
    $metricId = metric($db, $target);
    $from = new DateTime($from);
    $until = new DateTime($until);

    $current = new DateTime($from->format('Y-m-01 00:00:00'));
    $result = array();

    while (true) {
        $last = new DateTime($current->format('Y-m-01 23:59:59'));
        $last->modify('next month')->modify('previous day');

        $result = combine($result, select_range($db, $metricId, $current->format('Y-m-d 00:00:00'), $last->format('Y-m-d 23:59:59')));

        $current->modify('next month');

        if ($current->format('Ym') > $until->format('Ym')) {
            break;
        }
    }

    return filter($result, $from, $until);
}

function save_message($db, $message)
{
    $start = microtime(true);
    insert_message($db, $message);
    metric_collect('metrics.server.elapsed', (microtime(true) - $start) * 1000);
}

function insert_message($db, $message = null, $flush = false)
{
    static $messages = array();

    $start = microtime(true);

    if (is_string($message)) {
        $messages[] = $message;
    }

    if (count($messages) == 100 || $flush) {
        save_messages($db, $messages);
        $messages = array();
    }
    metric_collect('metrics.insert_message.elapsed', (microtime(true) - $start) * 1000);
}

function save_messages($db, $messages)
{
    $items = array();

    foreach ($messages as $message) {
        list($target, $value, $timestamp) = explode(' ', trim($message));
        $metricId = metric($db, $target);
        $created = date('Y-m-d H:i:s', (int)$timestamp);
        $items[] = array($metricId, $value, $created);
    }

    insert_multi($db, $items);
}

function insert_multi($db, $items)
{
    $start = microtime(true);

    $placeholders = array_fill(0, count($items), '(?, ?, ?)');
    $data = array();

    foreach ($items as $item) {
        $data = array_merge($data, array_values($item));
    }

    $insert = $db->prepare("INSERT INTO data (metric_id, value, created) VALUE " . implode(', ', $placeholders));
    $insert->execute($data);

    metric_collect('metrics.insert.elapsed', (microtime(true) - $start) * 1000);
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
function select_range($db, $metricId,  $from, $until)
{
    return combine(
        select($db, $metricId, $from, $until, ''),
        select($db, $metricId, $from, $until, '_5min'),
        select($db, $metricId, $from, $until, '_60min')
    );
}

function select($db, $metricId, $from, $until, $interval = '', $group = 60)
{
    $select = $db->prepare("
        SELECT FROM_UNIXTIME(FLOOR(UNIX_TIMESTAMP(created) / :group) * :group) AS time,
        SUM(value) sum,
        COUNT(id) count
        FROM data${interval}
        WHERE metric_id = :metric_id
        AND created BETWEEN :from AND :until
        GROUP BY time
    ");

    $select->execute(array(
        ':metric_id' => $metricId,
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
            $key = $item['time'];
            $order[$key] = $item['time'];

            if (array_key_exists($item['time'], $result)) {
                $result[$key]['sum'] += $item['sum'];
                $result[$key]['count'] += $item['count'];
            } else {
                $result[$item['time']] = $item;
            }
        }
    }

    array_multisort($order, $result, SORT_ASC);
    return $result;
}
