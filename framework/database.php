<?php
require_once dirname(__FILE__) . '/config.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$capsule->addConnection($config['db']);

$capsule->bootEloquent();
