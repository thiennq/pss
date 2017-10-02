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

$Schema->create('menu', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('parent_id')->nullable();
    $table->string('title');
    $table->string('link')->nullable();
    $table->string('link_type')->nullable();
    $table->timestamps();
});
