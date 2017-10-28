<?php

$dotenv = new Dotenv\Dotenv(dirname(dirname(__FILE__)));
$dotenv->load();

$db = [
    'driver' => 'mysql',
    'host' => getenv('DB_HOST') ? getenv('DB_HOST') : 'localhost',
    'port' => getenv('DB_PORT') ? getenv('DB_PORT') : 3306,
    'database' => getenv('DATABASE') ? getenv('DATABASE') : 'skeleton',
    'username' => getenv('DB_USER') ? getenv('DB_USER') : 'root',
    'password' => getenv('DB_PASSWORD') ? getenv('DB_PASSWORD') : '',
    'prefix'    => getenv('DB_PREFIX') ? getenv('DB_PREFIX') : '',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
];

$onesignal= [
    'app_id' => getenv('ONESIGNAL_APP_ID') ? getenv('ONESIGNAL_APP_ID') : '',
    'rest_key' => getenv('ONESIGNAL_REST_KEY') ? getenv('ONESIGNAL_REST_KEY') : '',
    'safari_key' => getenv('ONESIGNAL_SAFARI_KEY') ?getenv('ONESIGNAL_SAFARI_KEY') : '',
    'subdomain' => getenv('ONESIGNAL_SUBDOMAIN') ? getenv('ONESIGNAL_SUBDOMAIN') : ''
];

if (getenv('UNIX_SOCKET')) {
  $db['unix_socket'] = getenv('UNIX_SOCKET');
}

$config = [
    'determineRouteBeforeAppMiddleware' => false,
    'displayErrorDetails' => true,
    'db' => $db,
    'onesignal' => $onesignal,
    'themeDir' => 'default'
];

$config['VIEW_ENGINE'] = getenv('VIEW_ENGINE') ? getenv('VIEW_ENGINE') : 'pug';
