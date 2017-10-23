<?php
$MEMCACHED_HOST =  getenv('DB_HOST') ? getenv('DB_HOST') : 'localhost';
$MEMCACHED_PORT =  11211;
$GLOBALS['memcached'] = new Memcached;
$GLOBALS['memcached']->addServer($MEMCACHED_HOST, $MEMCACHED_PORT);
