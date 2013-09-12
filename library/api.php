<?php

function metric_collect($target, $value, $timestamp = 'now')
{
    $message = sprintf('%s %s %s', $target, $value, date('U', strtotime($timestamp)));

    $socket = fsockopen('udp://localhost', 2003);
    fwrite($socket, $message);
}
