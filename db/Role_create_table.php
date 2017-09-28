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

$Schema->create('role', function (Blueprint $table) {
  $table->increments('id');
  $table->string('email');
  $table->boolean('product');
  $table->boolean('order');
  $table->boolean('customer');
  $table->boolean('article');
  $table->boolean('setting');
  $table->boolean('staff');
  $table->timestamps();
});
