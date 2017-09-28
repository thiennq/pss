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

$Schema->create('branch', function (Blueprint $table) {
  $table->increments('id');
  $table->string('region_id')->nullable();
  $table->string('name');
  $table->text('address')->nullable();
  $table->string('hotline')->nullable();
  $table->string('featured_image')->nullable();
  $table->string('open_hours')->nullable();
  $table->string('close_hours')->nullable();
  $table->text('link')->nullable();
  $table->boolean('display')->nullable();
  $table->boolean('calc_inventory')->nullable();
  $table->boolean('branch_center')->nullable();
  $table->timestamps();
});
