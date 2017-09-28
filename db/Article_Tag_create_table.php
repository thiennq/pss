<?php

require( '../vendor/autoload.php' );
require( '../framework/config.php' );

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;


$capsule = new Capsule;
$capsule->addConnection( $config['db'] );
$capsule->setAsGlobal();
$capsule->bootEloquent();

date_default_timezone_set( 'Asia/Ho_Chi_Minh' );

$schema = $capsule->schema();

$schema->create( 'tag', function( Blueprint $table ) {
	$table->increments( 'id' );
	$table->string( 'title' );
	$table->timestamps();
} );

$schema->create( 'article_tag', function( Blueprint $table ) {
	$table->increments( 'id' );
	$table->integer( 'article_id' );
	$table->integer( 'tag_id' );
	$table->timestamps();
} );
