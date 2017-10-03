<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once('../controllers/IndexController.php');
require_once('../controllers/CollectionController.php');
require_once('../controllers/ProductController.php');
require_once('../controllers/MetaController.php');
require_once('../controllers/PageController.php');
require_once('../controllers/OrderController.php');
require_once('../controllers/CustomerController.php');
require_once('../controllers/ArticleController.php');
require_once('../controllers/FunctionController.php');
require_once('../controllers/CrawlerController.php');
require_once('../controllers/TestController.php');
require_once('../models/helper.php');

// $currentUrl = HOST . $_SERVER['REQUEST_URI'];
// $redirect = Redirect::where('old', $currentUrl)->first();
// if($redirect) {
//   header("HTTP/1.1 301 Moved Permanently");
//   header("Location: ". $redirect->new);
//   exit();
// }
//
// if(strpos($currentUrl, 'products')) {
//   $currentUrl = str_replace('products', 'san-pham', $currentUrl);
//   header("HTTP/1.1 301 Moved Permanently");
//   header("Location: ". $currentUrl);
//   exit();
// }
//
// if(strlen($_SERVER['REQUEST_URI']) > 2 && substr($_SERVER['REQUEST_URI'], -1) == '/') {
//   $currentUrl = substr($currentUrl, 0, -1);
//   header("HTTP/1.1 301 Moved Permanently");
//   header("Location: ". $currentUrl);
//   exit();
// }

$app->get('/', '\IndexController:index');
$app->get('/dat-hang', '\OrderController:checkout');
$app->get('/orders/{id}', '\OrderController:show');
$app->get('/video', '\PageController:video');
$app->get('/saleoff', '\PageController:saleOff');
$app->get('/thuong-hieu/{name}', '\CollectionController:brand');
$app->get('/thuong-hieu', '\CollectionController:listAllBrand');
$app->get('/tim-kiem', '\CollectionController:search');
$app->get('/tin-tuc', '\ArticleController:news');
$app->get('/he-thong-cua-hang', '\PageController:branch');
$app->get('/hang-moi-ve', '\CollectionController:newProduct');
$app->get('/giam-gia-50', '\CollectionController:discount50');
$app->get('/san-pham/{handle}', '\ProductController:show');
$app->get('/dat-hang-thanh-cong', '\OrderController:orderSuccess');
$app->get('/tag/{handle}', '\CollectionController:showTag');

$app->get('/khuyen-mai/{link}', '\ArticleController:getPromotion');
$app->get('/tin-tuc/{link}', '\ArticleController:getNews');
$app->get('/thong-tin/{link}', '\ArticleController:getInfo');

$app->post('/api/filter', '\CollectionController:filter');
$app->get('/api/san-pham/search', '\ProductController:smartSearch');
$app->get('/api/region', '\FunctionController::getSubRegion');
$app->post('/api/orders', '\OrderController:store');
$app->get('/api/website/sitemap', '\FunctionController:SiteMap' );
$app->get('/api/website/initDB', '\FunctionController:initDB');
$app->get('/404', '\ArticleController:PageNotFound');
$app->post('/api/addToCart', '\OrderController:addToCart');
$app->put('/api/updateCart', '\OrderController:updateCart');
$app->delete('/api/deleteCart', '\OrderController:deleteCart');

$app->get('/api/san-pham/modal/{id}', '\ProductController:findProductModal');
$app->get('/api/san-pham/variant/{id}', '\ProductController:findProductVariant');
$app->get('/api/crawler/getAllProduct', '\CrawlerController:getAllProduct');
$app->get('/api/crawler/getAllBranch', '\CrawlerController:getAllBranch');
$app->get('/api/crawler/updateInventory', '\CrawlerController:updateInventory');
$app->get('/api/crawler/updateInventoryOneBranch', '\CrawlerController:updateInventoryOneBranch');
$app->get('/api/crawler/updateOrderState', '\CrawlerController:updateOrderState');
$app->get('/api/crawler/updateOrderState/{id}', '\CrawlerController:updateOneOrderState');
$app->get('/api/crawler/getListDistrict', '\CrawlerController:getListDistrict');
$app->get('/api/crawler/getListCity', '\CrawlerController:getListCity');
$app->get('/api/crawler/createMeta', '\CrawlerController:createMeta');

$app->post('/api/subscribe', '\CustomerController:subscribe');

$app->get('/test/test', '\TestController:test');

$app->get('/test-mail', '\TestController:sendMail');

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
