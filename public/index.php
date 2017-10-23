<?php
error_reporting(0);
ini_set('memory_limit', '-1');

use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once('../vendor/autoload.php');

date_default_timezone_set(getenv('TIMEZONE') ? getenv('TIMEZONE') : 'Asia/Ho_Chi_Minh');
define('ROOT', dirname(dirname(__FILE__)));
define('CONFIGPATH', dirname(dirname(__FILE__)));

try {
  $PROTOCOL = isset($_SERVER["HTTPS"]) ? 'https' : 'http';
  $HOST = $PROTOCOL . '://' . $_SERVER['HTTP_HOST'];
  define('HOST', $HOST);
  define('PROTOCOL', $PROTOCOL);
} catch (Exception $e) {
  error_log($e->getMessage());
}


require_once('../framework/config.php');
require_once('../framework/database.php');
require_once('../framework/controller.php');
require_once('../framework/adminController.php');
if (getenv('ENV') && getenv('ENV') == 'production') {
  require_once('../framework/memcached.php');
}

$app = new \Slim\App(['settings'  => [
    'determineRouteBeforeAppMiddleware' => true,
  ]
]);
$container = $app->getContainer();

$container['logger'] = function($c) {
  $logger = new \Monolog\Logger('my_logger');
  $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
  $logger->pushHandler($file_handler);
  return $logger;
};

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
      $ctrl = new Controller($c);
      return $ctrl->view->render($response,'404.pug', []);
    };
};


$files = glob('../routes/*.php');
foreach ($files as $file) {
  require_once($file);
}

$app->run();
