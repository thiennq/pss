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

$Schema->create('collection', function (Blueprint $table) {
    $table->increments('id');
    $table->string('parent_id');
    $table->string('title');
    $table->string('image')->nullable();
    $table->text('banner')->nullable();
    $table->string('handle')->nullable();
    $table->string('breadcrumb')->nullable();
    $table->string('link')->nullable();
    $table->text('description')->nullable();
    $table->text('content')->nullable();
    $table->timestamps();
});
