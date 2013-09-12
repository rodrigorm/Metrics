<?php

function metric_collect($target, $value, $timestamp = 'now')
{
    $message = sprintf('%s %s %s', $target, $value, date('U', strtotime($timestamp)));

    $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    socket_sendto($socket, $message, strlen($message), 0, 'localhost', 2003);
}
