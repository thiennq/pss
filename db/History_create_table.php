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

$Schema->create('history', function (Blueprint $table) {
  $table->increments('id');
  $table->string('user');
  $table->integer('user_id')->nullable();
  $table->text('content')->nullable();
  $table->string('type');
  $table->string('type_id')->nullable();
  $table->timestamps();
});
