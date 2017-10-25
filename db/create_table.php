<?php

require('../vendor/autoload.php');
require('../framework/config.php');
define('ROOT', dirname(dirname(__FILE__)));

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

  $files = scandir('.');
  $files = array_diff($files, array('.', '..', __FILE__));
  foreach ($files as $file) {
    try {
      if ($file != 'seeder.php') {
        error_log($file);
        require_once($file);
      }
    } catch (Exception $e) {
      echo $e;
    }
  }

  $capsule = new Capsule;
  $capsule->addConnection($config['db']);
  $capsule->setAsGlobal();
  $capsule->bootEloquent();

  Capsule::insert('INSERT INTO ' . Capsule::getTablePrefix() . 'user (id, name, email, phone, password, role, random, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', [1, 'Super', 'admin@eyeteam.vn', '0123456789', '$2y$10$GqGH78I8ZHIrywkNbdylAOpP2zJRz/L8K8WlEZoN1vnJwI90ndhvq', 'super', '', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]);

  if (!file_exists(ROOT . '/public/uploads')) {
    mkdir(ROOT . '/public/uploads', 0777, true);
  }

  if (!file_exists(ROOT . '/public/images')) {
    mkdir(ROOT . '/public/images', 0777, true);
  }
