<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once('../controllers/admin/AdminCollectionController.php');
require_once('../controllers/admin/AdminProductController.php');
require_once('../controllers/admin/AdminVariantController.php');
require_once('../controllers/admin/AdminSliderController.php');
require_once('../controllers/admin/AdminMenuController.php');
require_once('../controllers/admin/AdminOrderController.php');
require_once('../controllers/admin/AdminCustomerController.php');
require_once('../controllers/admin/AdminVideoController.php');
require_once('../controllers/admin/AdminArticleController.php');
require_once('../controllers/admin/AdminBlogController.php');
require_once('../controllers/admin/AdminPageController.php');
require_once('../controllers/admin/AdminBrandController.php');
require_once('../controllers/admin/AdminAttributeController.php');
require_once('../controllers/admin/AdminUserController.php');
require_once('../controllers/admin/AdminSettingController.php');
require_once('../controllers/admin/AdminPriceController.php');
require_once('../controllers/admin/AdminRedirectController.php');
require_once('../controllers/TestController.php');
require_once("../models/User.php");

$role = $_SESSION['role'];

$app->get("/admin", function ($req, $res){
  if(isset($_SESSION['href'])) return $res->withStatus(302)->withHeader('Location', $_SESSION['href']);
  return $res->withStatus(302)->withHeader('Location', '/admin/login');
});

$app->group('/admin', function() use($app) {

  $app->get("/", function ($req, $res){
    $href = $_SESSION['href'];
    return $res->withStatus(302)->withHeader('Location', $href);
  });

  $app->get('/login', '\AdminUserController:getlogin');
  $app->get('/logout', '\AdminUserController:getLogout');

  $app->post('/api/uploadImage', 'uploadImage');
  $app->post('/api/uploadImageTinymce', 'uploadImageTinymce');

  //Product
  $app->get('/product', '\AdminProductController:fetch');
  $app->post('/product', '\AdminProductController:store');
  $app->get('/product/new', '\AdminProductController:create');
  $app->get('/product/{id}', '\AdminProductController:show');
  $app->put('/product/{id}', '\AdminProductController:update');
  $app->put('/product/featured-image/{id}', '\AdminProductController:updateFeaturedImage');
  $app->delete('/product/{id}', '\AdminProductController:delete');

  $app->post('/variants', '\AdminVariantController:store');
  $app->get('/variants/{id}', '\AdminVariantController:show');
  $app->put('/variants/{id}', '\AdminVariantController:update');
  $app->delete('/variants/{id}', '\AdminVariantController:delete');

  $app->get('/collection', '\AdminCollectionController:index');
  $app->get('/collection/new', '\AdminCollectionController:create');
  $app->get('/collection/{id}', '\AdminCollectionController:show');
  $app->post('/collection', '\AdminCollectionController:store');
  $app->put('/collection/{id}', '\AdminCollectionController:update');
  $app->delete('/collection/{id}', '\AdminCollectionController:delete');

  $app->get('/attribute', '\AdminAttributeController:index');
  $app->post('/attribute', '\AdminAttributeController:store');
  $app->get('/attribute/{id}', '\AdminAttributeController:get');
  $app->put('/attribute/{id}', '\AdminAttributeController:update');
  $app->delete('/attribute/{id}', '\AdminAttributeController:delete');

  $app->get('/brand', '\AdminBrandController:index');
  $app->post('/brand', '\AdminBrandController:store');
  $app->get('/brands/{id}', '\AdminBrandController:show');
  $app->put('/brand/{id}', '\AdminBrandController:update');
  $app->delete('/brand/{id}', '\AdminBrandController:delete');

  $app->get('/price', '\AdminPriceController:index');
  $app->post('/price', '\AdminPriceController:store');
  $app->put('/price/{id}', '\AdminPriceController:update');
  $app->delete('/price/{id}', '\AdminPriceController:delete');


  //Order
  $app->get('/order', '\AdminOrderController:index');
  $app->get('/orders/{id}', '\AdminOrderController:show');
  $app->put('/orders/{id}', '\AdminOrderController:update');


  //Customer
  $app->get('/customer', '\AdminCustomerController:index');
  $app->get('/customer/{id}/order', '\AdminCustomerController:showOrder');
  $app->get('/customer/export', '\AdminCustomerController:export');

  // Article
  $app->get('/article/new', '\AdminArticleController:create');
  $app->get('/articles', '\AdminArticleController:fetch');
  $app->get('/article/{id}', '\AdminArticleController:get');
  $app->post('/article', '\AdminArticleController:store');
  $app->put('/article/{id}', '\AdminArticleController:update');
  $app->delete('/article/{id}', '\AdminArticleController:delete');

  // Blog
  $app->get('/blog/new', '\AdminBlogController:create');
  $app->get('/blogs', '\AdminBlogController:fetch');
  $app->get('/blog/{id}', '\AdminBlogController:get');
  $app->post('/blog', '\AdminBlogController:store');
  $app->put('/blog/{id}', '\AdminBlogController:update');
  $app->delete('/blog/{id}', '\AdminBlogController:delete');

  // Page
  $app->get('/page/new', '\AdminPageController:create');
  $app->get('/pages', '\AdminPageController:fetch');
  $app->get('/page/{id}', '\AdminPageController:get');
  $app->post('/page', '\AdminPageController:store');
  $app->put('/page/{id}', '\AdminPageController:update');
  $app->delete('/page/{id}', '\AdminPageController:delete');


  $app->get('/redirect', '\AdminRedirectController:index');
  $app->post('/redirect', '\AdminRedirectController:store');
  $app->put('/redirect/{id}', '\AdminRedirectController:update');
  $app->delete('/redirect/{id}', '\AdminRedirectController:delete');
  $app->post('/redirect/import', '\AdminRedirectController:postImport');

  $app->get('/slider', '\AdminSliderController:index');
  $app->get('/slider/{id}', '\AdminSliderController:getSlider');
  $app->post('/slider', '\AdminSliderController:store');
  $app->put('/slider/{id}', '\AdminSliderController:update');
  $app->delete('/slider/{id}', '\AdminSliderController:delete');

  $app->get('/menu', '\AdminMenuController:index');
  $app->get('/menu/{id}', '\AdminMenuController:getMenu');
  $app->post('/menu', '\AdminMenuController:store');
  $app->delete('/menu/{id}', '\AdminMenuController:delete');
  $app->put('/menu/{id}', '\AdminMenuController:update');
  $app->get('/menu/list-menu/{type}', '\AdminMenuController:getListMenu');

  $app->get('/setting', '\AdminSettingController:setting');
  $app->put('/setting', '\AdminSettingController:updateSetting');
  $app->get('/video', '\AdminVideoController:index');
  $app->post('/video', '\AdminVideoController:store');
  $app->put('/video/{id}', '\AdminVideoController:update');
  $app->delete('/video/{id}', '\AdminVideoController:delete');
  $app->get('/images', '\AdminSettingController:getImages');
  $app->delete('/api/images/remove', '\AdminSettingController:removeImage');

  //User
  $app->group('', function () use ($app) {
    $app->get('/user', '\AdminUserController:index');
    $app->get('/api/user/{id}', '\AdminUserController:show');
    $app->post('/api/user', '\AdminUserController:store');
    $app->put('/api/user/changePassword', '\AdminUserController:changePassword');
    $app->put('/api/user/{id}', '\AdminUserController:update');
    $app->delete('/api/user/{id}', '\AdminUserController:delete');
  })->add(function ($request, $response, $next) {
    if(in_array('admin', $_SESSION['role'])) return $next($request, $response);
    return $response->withStatus(302)->withHeader('Location', '/404');
  });

  $app->get('/api/rotate', 'rotateImage');
  $app->get('/api/tinymce/images', '\AdminProductController:renderImageTinymce');

})->add(function ($request, $response, $next) {
  if(session_status() == PHP_SESSION_NONE) session_start();
  if(isset($_SESSION['login'])) {
    $user = User::find($_SESSION['user_id']);
    if(!$user) {
      unset($_SESSION['login']);
      unset($_SESSION['user_id']);
      unset($_SESSION['email']);
      unset($_SESSION['name']);
      unset($_SESSION['role']);
      return $response->withStatus(302)->withHeader('Location', '/admin/login');
    }
  }
  if((in_array('login', $_SESSION) && $_SESSION['login']) || strpos($request->getUri()->getPath(), "/login") !== false) return $next($request, $response);
  $_SESSION['href'] = $request->getUri()->getPath();
  return $response->withStatus(302)->withHeader('Location', '/admin/login');
});
