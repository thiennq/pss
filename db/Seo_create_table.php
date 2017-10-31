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

$Schema->create('seo', function (Blueprint $table) {
  $table->increments('id');
  $table->text('meta_title')->nullable();
  $table->text('meta_description')->nullable();
  $table->text('meta_keyword')->nullable();
  $table->text('meta_robots')->nullable();
  $table->string('type')->nullable();
  $table->integer('type_id')->nullable();
  $table->timestamps();
});
