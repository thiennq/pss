<?php

require_once (dirname(__FILE__) . '/config.php');
require_once (dirname(__FILE__) . '/../vendor/pug-php/pug/src/Pug/Pug.php');
use \Pug\Pug as Pug;
use Jenssegers\Blade\Blade as Blade;
require_once (ROOT . '/framework/pug-helper.php');

class View {
  public function __construct($obj) {
    global $config;
    $this->path = $obj['path'];
    $this->device = $obj['device'];
    $this->layout = $obj['layout'];
    $this->engine = $config['VIEW_ENGINE'];
    if ($this->layout == 'admin') {
      $this->engine = 'pug';
    }
    error_log('this->engine = ' . $this->engine);

    $PUG_PRETTY = getenv('PUG_PRETTY') ? getenv('PUG_PRETTY') : false;
    $CACHE = getenv('CACHE') ? getenv('CACHE') : '';
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

    if ($this->engine == 'pug') {
      $view = new Pug($viewConfig);
    } else if ($this->engine == 'blade') {
      $view = new Blade($obj['path'] . 'blade/', $viewConfig['cache']);
    }
    else if ($this->engine == 'twig') {
      if ($this->layout == 'theme') {
        $filepath = $this->path . 'twig/';
      }
      require_once (ROOT . '/framework/twig-helper.php');
      $view = new \Slim\Views\Twig($filepath, $viewConfig);
      $view->addExtension(new Twig_Helper());
    }
    $this->view = $view;
  }

  public function render($response, $file, $data = array()) {
    try {
      global $config, $pugVars;
      $data['config'] = $config;
      foreach ($pugVars as $key => $val) {
        if (!isset($data[$key])) {
          $data[$key] = $val;
        }
      }
      $data['device'] = $this->device;

      if ($this->engine == 'pug') {
        $filepath = $this->path . $file . '.pug';
        if ($this->layout == 'theme') {
          $filepath = $this->path . 'pug/' . $file . '.pug';
        }
        $html = $this->view->render($filepath, $data);
        return $response->write($html);
      } else if ($this->engine == 'blade') {
        $html = $this->view->make($file, $data);
        return $response->write($html);
      } else if ($this->engine == 'twig') {
        return $this->view->render($response, $file . '.html' , $data);
      }
    } catch (Exception $e) {
      echo $e;
    }
 }

  public function get($file, $data) {
    $filepath = $this->path . $file;
    return $this->view_engine->render($filepath, $data);
  }
}
