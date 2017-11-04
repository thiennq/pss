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

$Schema->create('coupon', function (Blueprint $table) {
    $table->increments('id');
    $table->string('title');
    $table->string('code');
    $table->string('type');
    $table->integer('value');
    $table->integer('min_value_order');
    $table->integer('usage_count');
    $table->integer('usage_left');
    $table->text('description')->nullable();
    $table->string('expired_date');
    $table->timestamps();
});
