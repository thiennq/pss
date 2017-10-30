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

$Schema->create('comment', function (Blueprint $table) {
    $table->increments('id');
    $table->string('name');
    $table->string('phone_number')->nullable();
    $table->string('email')->nullable();
    $table->text('content');
    $table->integer('parent_id');
    $table->string('type'); //bai viet hoặc san pham
    $table->integer('type_id'); //id cua bai viet or san pham
    $table->tinyInteger('status'); // 0, 1
    $table->timestamps();
});
