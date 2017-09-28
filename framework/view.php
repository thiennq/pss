<?php

require_once (dirname(__FILE__) . '/config.php');
require_once (dirname(__FILE__) . '/../vendor/pug-php/pug/src/Pug/Pug.php');
use \Pug\Pug as Pug;
require_once (dirname(__FILE__) . '/pug-helper.php');

class View {
  public function __construct($obj) {
    $this->path = $obj['path'];
    $this->device = $obj['device'];

    $PUG_PRETTY = getenv('PUG_PRETTY') ? getenv('PUG_PRETTY') : false;
    $CACHE = getenv('CACHE') ? getenv('CACHE') : false;
    $viewConfig = array(
      'prettyprint' => $PUG_PRETTY,
      'debug' => false,
      'expressionLanguage' => 'js'
    );
    if ($CACHE && strlen($CACHE) > 0) {
      $viewConfig['cache'] = $CACHE;
    }

    // override config from config.php
    if (isset($config['view'])) {
      foreach ($config['view'] as $key => $value) {
        $viewConfig[$key] = $value;
      }
    }
    $pug = new Pug($viewConfig);
    $this->pug = $pug;
  }

  public function render($response, $file, $data = array()) {
    try {
      global $config, $pugVars;

      $filepath = $this->path . $file;
      $data['config'] = $config;
      foreach ($pugVars as $key => $val) {
        if (!isset($data[$key])) {
          $data[$key] = $val;
        }
      }
      $data['device'] = $this->device;

      $html = $this->pug->render($filepath, $data);
      return $response->write($html);
    } catch (Exception $e) {
      echo $e;
    }
 }

  public function get($file, $data) {
    $filepath = $this->path . $file;
    return $this->pug->render($filepath, $data);
  }
}
