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

$Schema->create('article', function (Blueprint $table) {
  $table->increments('id');
  $table->string('collection_id')->nullable();
  $table->string('title');
  $table->string('handle');
  $table->string('link');
  $table->string('type');
  $table->string('image')->nullable();
  $table->text('description')->nullable();
  $table->text('description_seo')->nullable();
  $table->text('content');
  $table->text('content_promotion')->nullable();
  $table->string('author')->nullable();
  $table->boolean('display');
  $table->integer('view');
  $table->timestamps();
});
