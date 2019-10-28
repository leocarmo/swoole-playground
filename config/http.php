<?php

return [
    'daemonize'             => false,
    'dispatch_mode'         => 1,
    'max_request'           => 8000,
    'open_tcp_nodelay'      => true,
    'reload_async'          => true,
    'max_wait_time'         => 60,
    'enable_reuse_port'     => true,
    'enable_coroutine'      => true,
    'http_compression'      => false,
    'enable_static_handler' => false,
    'buffer_output_size'    => 4 * 1024 * 1024,
    'worker_num'            => 4, // Each worker holds a connection pool
];