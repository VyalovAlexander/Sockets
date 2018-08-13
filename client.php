#! /usr/bin/env php
<?php

$params = getopt('', ['address::', 'port::', 'message::']);
$address = $params['address'] ?? '127.0.0.1';
$port = $params['port'] ?? '9999';
$message = $params['message'] ?? 'hi';

while (true) {

    usleep(100000);
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if ($socket == false)
    {
        die('socket created failed' . socket_strerror(socket_last_error()));
    }
    $connect = socket_connect($socket, $address, $port);

    if ($connect == false) {
        die('socket connect failed' . socket_strerror(socket_last_error()));
    }

    socket_write($socket, $message, strlen($message));

    $answer = '';

    while (($line = socket_read($socket, 2048)) !== "") {
        $answer .= $line;
    }

    print $answer;
    socket_close($socket);
}
