<?php

require('../vendor/autoload.php');
require('../framework/config.php');

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;


$capsule = new Capsule;
$capsule->addConnection($config['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

Capsule::insert('INSERT INTO ' . Capsule::getTablePrefix() . 'user (id, name, email, phone, password, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [1, 'Super', 'admin@eyeteam.vn', '0123456789', '$2y$10$GqGH78I8ZHIrywkNbdylAOpP2zJRz/L8K8WlEZoN1vnJwI90ndhvq', 'super', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]);
