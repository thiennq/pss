<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once('../controllers/IndexController.php');
require_once('../controllers/CollectionController.php');
require_once('../controllers/ProductController.php');
require_once('../controllers/PageController.php');
require_once('../controllers/OrderController.php');
require_once('../controllers/ArticleController.php');
require_once('../controllers/BlogController.php');
require_once('../controllers/TestController.php');
require_once('../models/helper.php');


$app->get('/', '\IndexController:index');

$app->get('/video', '\PageController:video');
$app->get('/saleoff', '\PageController:saleOff');
$app->get('/thuong-hieu/{name}', '\CollectionController:brand');
$app->get('/tim-kiem', '\CollectionController:search');
$app->get('/san-pham/{handle}', '\ProductController:show');
$app->get('/dat-hang-thanh-cong', '\OrderController:orderSuccess');

$app->get('/blog/{handle}', '\BlogController:get');
$app->get('/article/{handle}', '\ArticleController:get');
$app->get('/page/{handle}', '\PageController:get');
$app->get('/cart', '\OrderController:viewCart');
$app->get('/checkout', '\OrderController:checkOut');

$app->get('/404', '\PageController:PageNotFound');

$app->post('/api/filter', '\CollectionController:filter');
$app->get('/api/san-pham/search', 'smartSearch');
$app->get('/api/region', 'getSubRegion');
$app->post('/api/orders', '\OrderController:store');
$app->get('/api/website/sitemap', 'createSitemap' );

$app->get('/api/getInfoCart', '\OrderController:getInfoCart');
$app->post('/api/addToCart', '\OrderController:addToCart');
$app->put('/api/updateCart', '\OrderController:updateCart');
$app->delete('/api/deleteCart', '\OrderController:deleteCart');

$app->get('/api/san-pham/variant/{id}', '\ProductController:findProductVariant');
$app->get('/test-mail', '\TestController:sendMail');
$app->get('/truyen', '\TestController:truyen');


$app->get('/{params:.*}', function($request, $response, $args) {
  $link = $request->getAttribute('params');
  $params = explode('/', $link);
  if($params[count($params) - 1] == '') array_pop($params);
  $CollectionCtrl = new CollectionController(new ContainerInterface);

  if (count($params) == 0) {
    $handle = $params[count($params) - 1];
    $request = $request->withAttribute('handle', $handle);
    $request = $request->withAttribute('link', $link);
    $result = $CollectionCtrl->show->index($request, $response);
  } else {
    $handle = $params[count($params) - 1];
    $request = $request->withAttribute('handle', $handle);
    $request = $request->withAttribute('link', $link);
    $result = $CollectionCtrl->show($request, $response);
    if(!$result) {
      $response = $response->withStatus(404);
      return $response;
    }
    return $result;
  }
});

//Redirect to 404
$app->group('/{link}', function() use($app) {
  $app->get('', function($request, $response, $args) {
    return $response->withStatus(302)->withHeader('Location', '/404');
  });
});
