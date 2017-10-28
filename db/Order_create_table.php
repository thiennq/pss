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

$Schema->create('order', function (Blueprint $table) {
    $table->increments('id');
    $table->string('customer_id');
    $table->string('payment_method');
    $table->string('shipping_price');
    $table->string('discount');
    $table->string('order_status'); // new, confim, done, cancel, return
    $table->integer('payment_status'); // 0, 1
    $table->integer('shipping_status'); // 0, 1, 2
    $table->string('subtotal');
    $table->string('total');
    $table->text('notes')->nullable();
    $table->text('reason_cancel')->nullable();
    $table->timestamps();
});
