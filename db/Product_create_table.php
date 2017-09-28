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

$Schema->create('product', function (Blueprint $table) {
    $table->increments('id');
    $table->string('group_id')->nullable();
    $table->string('title');
    $table->string('handle');
    $table->string('barcode');
    $table->integer('price');
    $table->integer('price_compare')->nullable();
    $table->integer('discount')->nullable();
    $table->text('content')->nullable();
    $table->text('description')->nullable();
    $table->text('meta_description')->nullable();
    $table->text('material')->nullable();
    $table->text('specification')->nullable();
    $table->string('brand')->nullable();
    $table->string('color')->nullable();
    $table->integer('sell')->nullable();
    $table->boolean('display')->nullable();
    $table->boolean('dropship')->nullable();
    $table->integer('in_stock')->nullable();
    $table->integer('view')->nullable();
    $table->timestamps();
});
