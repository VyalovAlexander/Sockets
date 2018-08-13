#! /usr/bin/env php
<?php

$params = getopt('', ['address::', 'port::', 'threads::']);
$address = $params['address'] ?? '127.0.0.1';
$port = $params['port'] ?? '9999';
$port = $params['port'] ?? '9999';
$threads = $params['threads'] ?? '1';

$acceptor = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($acceptor == false)
{
    die('socket created failed' . socket_strerror(socket_last_error()));
}

socket_set_option($acceptor, SOL_SOCKET, SO_REUSEPORT, 1);
if (!socket_bind($acceptor, $address, $port))
{
    die('socket binding failed' . socket_strerror(socket_last_error()));
}

if(!socket_listen($acceptor, 1))
{
    die('socket listen failed' . socket_strerror(socket_last_error()));
}
for ($i = 0; $i < $threads; $i++) {

    $pid = pcntl_fork();

    if ($pid == 0) {
        while (true) {
            $socket = socket_accept($acceptor);

            socket_write($socket, 'Welcome from process' . posix_getpid());

            $command = trim(socket_read($socket, 2048));

            echo 'Retrieve command ' . $command;

            $message = "[" . $command . "]\n";

            socket_write($socket, $message);

            socket_close($socket);
        }
    }

    while (($cid = pcntl_waitpid(0, $status)) != -1) {
        $exitCode = pcntl_wexitstatus($status);
        echo "child " . $cid . " exit code = " . $exitCode;
    }
}
socket_close($acceptor);