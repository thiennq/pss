<?php

require('../vendor/autoload.php');
require('../framework/config.php');

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;


$capsule = new Capsule;
$capsule->addConnection($config['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

date_default_timezone_set('Asia/Ho_Chi_Minh');

$Schema = $capsule->schema();

$Schema->create('user', function (Blueprint $table) {
    $table->increments('id');
    $table->string('name')->nullable();
    $table->string('email');
    $table->string('phone');
    $table->string('password');
    $table->string('role');
    $table->timestamps();
});
