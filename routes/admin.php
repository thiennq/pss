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
require_once('../controllers/admin/AdminBranchController.php');
require_once('../controllers/admin/AdminBrandController.php');
require_once('../controllers/admin/AdminFilterController.php');
require_once('../controllers/admin/AdminUserController.php');
require_once('../controllers/admin/AdminSettingController.php');
require_once('../controllers/admin/AdminPriceController.php');
require_once('../controllers/admin/AdminRedirectController.php');
require_once('../controllers/admin/AdminCelebrityController.php');
require_once('../controllers/TestController.php');
require_once('../controllers/FunctionController.php');
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

  //Login
  $app->get('/login', '\AdminUserController:getlogin');
  $app->post('/login', '\AdminUserController:checkLogin');
  $app->get('/logout', '\AdminUserController:getLogout');

  $app->post('/api/uploadImage', 'uploadImage');
  $app->post('/api/uploadImageTinymce', 'uploadImageTinymce');

  //Product
  $app->get('/products', '\AdminProductController:index');
  $app->post('/products', '\AdminProductController:store');
  $app->get('/products/new', '\AdminProductController:create');
  $app->get('/products/{id}', '\AdminProductController:show');
  $app->put('/products/{id}', '\AdminProductController:update');
  $app->put('/products/featured-image/{id}', '\AdminProductController:updateFeaturedImage');
  $app->delete('/products/{id}', '\AdminProductController:delete');

  $app->post('/variants', '\AdminVariantController:store');
  $app->get('/variants/{id}', '\AdminVariantController:show');
  $app->put('/variants/{id}', '\AdminVariantController:update');
  $app->delete('/variants/{id}', '\AdminVariantController:delete');

  $app->get('/collections', '\AdminCollectionController:index');
  $app->get('/collections/new', '\AdminCollectionController:create');
  $app->get('/collections/{id}', '\AdminCollectionController:show');
  $app->post('/collections', '\AdminCollectionController:store');
  $app->put('/collections/{id}', '\AdminCollectionController:update');
  $app->delete('/collections/{id}', '\AdminCollectionController:delete');

  $app->get('/filters', '\AdminFilterController:index');
  $app->post('/filters', '\AdminFilterController:store');
  $app->get('/filters/{id}', '\AdminFilterController:get');
  $app->put('/filters/{id}', '\AdminFilterController:update');
  $app->delete('/filters/{id}', '\AdminFilterController:delete');

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
  $app->get('/subscribe', '\AdminCustomerController:subscribe');
  $app->get('/customer/{id}/order', '\AdminCustomerController:showOrder');
  $app->get('/customer/export', '\AdminCustomerController:export');

  // Article
  $app->get('/article/new', '\AdminArticleController:new');
  $app->get('/articles', '\AdminArticleController:fetch');
  $app->get('/article/{id}', '\AdminArticleController:get');
  $app->post('/article', '\AdminArticleController:create');
  $app->put('/article/{id}', '\AdminArticleController:update');
  $app->delete('/article/{id}', '\AdminArticleController:delete');
  $app->get('/articles/search', '\AdminArticleController:searchArticle');

  // Blog
  $app->get('/blog/new', '\AdminBlogController:new');
  $app->get('/blogs', '\AdminBlogController:fetch');
  $app->get('/blog/{id}', '\AdminBlogController:get');
  $app->post('/blog', '\AdminBlogController:create');
  $app->put('/blog/{id}', '\AdminBlogController:update');
  $app->delete('/blog/{id}', '\AdminBlogController:delete');
  $app->get('/blogs/search', '\AdminBlogController:searchBlog');

  // Page
  $app->get('/page/new', '\AdminPageController:new');
  $app->get('/pages', '\AdminPageController:fetch');
  $app->get('/page/{id}', '\AdminPageController:get');
  $app->post('/page', '\AdminPageController:create');
  $app->put('/page/{id}', '\AdminPageController:update');
  $app->delete('/page/{id}', '\AdminPageController:delete');
  $app->get('/pages/search', '\AdminPageController:searchPage');

  //Setting
  $app->get('/settings/index', '\AdminSettingController:settingIndex');
  $app->put('/api/settings/index', '\AdminSettingController:updateSettingIndex');
  $app->get('/shipping', '\AdminSettingController:settingShipping');
  $app->put('/api/settings/shipping', '\AdminSettingController:updateSettingShiping');

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

  $app->get('/celebrity', '\AdminCelebrityController:index');
  $app->get('/celebrity/{id}', '\AdminCelebrityController:getCelebrity');
  $app->post('/celebrity', '\AdminCelebrityController:store');
  $app->put('/celebrity/{id}', '\AdminCelebrityController:update');
  $app->delete('/celebrity/{id}', '\AdminCelebrityController:delete');

  $app->get('/menu', '\AdminMenuController:index');
  $app->get('/menu/{id}', '\AdminMenuController:getMenu');
  $app->post('/menu', '\AdminMenuController:store');
  $app->delete('/menu/{id}', '\AdminMenuController:delete');
  $app->put('/menu/{id}', '\AdminMenuController:update');
  $app->get('/menu/list-menu/{type}', '\AdminMenuController:getListMenu');

  $app->get('/information', '\AdminFilterController:showInformation');
  $app->get('/custom-css', '\AdminFilterController:showCustomCSS');
  $app->put('/information/custom-css', '\AdminFilterController:updateCustomCSS');

  $app->get('/settings/desktop', '\AdminSettingController:getDesktopSetting');
  $app->put('/settings/desktop', '\AdminSettingController:updateDesktopSetting');
  $app->get('/settings/mobile', '\AdminSettingController:getMobileSetting');
  $app->put('/settings/mobile', '\AdminSettingController:updateMobileSetting');
  $app->get('/settings/meta', '\AdminSettingController:getMetaTitleSetting');
  $app->put('/settings/metaTitle', '\AdminSettingController:updateMetaTitleSetting');
  $app->get('/settings/seo', '\AdminSettingController:getSEOSetting');
  $app->put('/settings/seo', '\AdminSettingController:updateSEOSetting');
  $app->get('/video', '\AdminVideoController:index');
  $app->post('/video', '\AdminVideoController:store');
  $app->put('/video/{id}', '\AdminVideoController:update');
  $app->delete('/video/{id}', '\AdminVideoController:delete');
  $app->get('/branch', '\AdminBranchController:index');
  $app->get('/branch/{id}', '\AdminBranchController:show');
  $app->put('/branch/{id}', '\AdminBranchController:update');
  $app->get('/livechat', '\AdminSettingController:getLiveChat');
  $app->post('/meta/saveMeta', '\AdminSettingController:saveMeta');
  $app->get('/images', '\AdminSettingController:getImages');
  $app->delete('/api/images/remove', '\AdminSettingController:removeImage');

  //Staff
  $app->get('/user', '\AdminUserController:index');
  $app->get('/user/{id}', '\AdminUserController:show');
  $app->post('/user', '\AdminUserController:store');
  $app->put('/user/doi-mat-khau', '\AdminUserController:changePassword');
  $app->put('/user/{id}', '\AdminUserController:update');
  $app->delete('/user/{id}', '\AdminUserController:delete');

  $app->get('/api/rotate', '\FunctionController:rotateImage');
  $app->get('/api/tinymce/images', '\AdminProductController:renderImageTinymce');

})->add(function ($request, $response, $next) {
  if(session_status() == PHP_SESSION_NONE) session_start();
  if(isset($_SESSION['login'])) {
    $user = User::find($_SESSION['user_id']);
    if(!$user) {
      unset($_SESSION['login']);
      unset($_SESSION['user_id']);
      unset($_SESSION['email']);
      unset($_SESSION['fullname']);
      unset($_SESSION['role']);
      return $response->withStatus(302)->withHeader('Location', '/admin/login');
    }
  }
  if((in_array('login', $_SESSION) && $_SESSION['login']) || strpos($request->getUri()->getPath(), "/login") !== false) return $next($request, $response);
  $_SESSION['href'] = $request->getUri()->getPath();
  return $response->withStatus(302)->withHeader('Location', '/admin/login');
});
