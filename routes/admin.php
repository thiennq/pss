<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once('../controllers/AdminCollectionController.php');
require_once('../controllers/AdminProductController.php');
require_once('../controllers/AdminSliderController.php');
require_once('../controllers/AdminMenuController.php');
require_once('../controllers/AdminOrderController.php');
require_once('../controllers/AdminCustomerController.php');
require_once('../controllers/AdminVideoController.php');
require_once('../controllers/AdminArticleController.php');
require_once('../controllers/AdminBlogController.php');
require_once('../controllers/AdminPageController.php');
require_once('../controllers/AdminBranchController.php');
require_once('../controllers/AdminColorController.php');
require_once('../controllers/AdminBrandController.php');
require_once('../controllers/AdminMaterialController.php');
require_once('../controllers/AdminSizeController.php');
require_once('../controllers/AdminSpecialController.php');
require_once('../controllers/AdminBagController.php');
require_once('../controllers/AdminFilterController.php');
require_once('../controllers/AdminUserController.php');
require_once('../controllers/AdminSettingController.php');
require_once('../controllers/FunctionController.php');
require_once('../controllers/AdminPriceController.php');
require_once('../controllers/AdminRedirectController.php');
require_once('../controllers/AdminCelebrityController.php');
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

  $app->get('/product', '\AdminProductController:index');
  $app->get('/san-pham/{id}', '\AdminProductController:show');
  $app->get('/product/new', '\AdminProductController:create');
  $app->put('/san-pham/{id}', '\AdminProductController:update');
  $app->delete('/api/product/image/{id}', '\AdminProductController:deleteImage');

  $app->get('/collection', '\AdminCollectionController:index');
  $app->get('/collections/{id}', '\AdminCollectionController:show');
  $app->get('/collection/new', '\AdminCollectionController:create');
  $app->post('/collection', '\AdminCollectionController:store');
  $app->put('/collections/{id}', '\AdminCollectionController:update');
  $app->delete('/collections/{id}', '\AdminCollectionController:delete');

  $app->get('/brand', '\AdminBrandController:index');
  $app->post('/brand', '\AdminBrandController:store');
  $app->get('/brands/{id}', '\AdminBrandController:show');
  $app->put('/brand/{id}', '\AdminBrandController:update');
  $app->delete('/brand/{id}', '\AdminBrandController:delete');

  $app->get('/color', '\AdminColorController:index');
  $app->post('/color', '\AdminColorController:store');
  $app->put('/color/{id}', '\AdminColorController:update');
  $app->delete('/color/{id}', '\AdminColorController:delete');

  $app->get('/material', '\AdminMaterialController:index');
  $app->post('/material', '\AdminMaterialController:store');
  $app->put('/material/{id}', '\AdminMaterialController:update');
  $app->delete('/material/{id}', '\AdminMaterialController:delete');

  $app->get('/size', '\AdminSizeController:index');
  $app->post('/size', '\AdminSizeController:store');
  $app->put('/size/{id}', '\AdminSizeController:update');
  $app->delete('/size/{id}', '\AdminSizeController:delete');

  $app->get('/special', '\AdminSpecialController:index');
  $app->post('/special', '\AdminSpecialController:store');
  $app->put('/special/{id}', '\AdminSpecialController:update');
  $app->delete('/special/{id}', '\AdminSpecialController:delete');

  $app->get('/bag', '\AdminBagController:index');
  $app->post('/bag', '\AdminBagController:store');
  $app->put('/bag/{id}', '\AdminBagController:update');
  $app->delete('/bag/{id}', '\AdminBagController:delete');

  $app->get('/specification', '\AdminProductController:specification');

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
  $app->get('/article/new', '\AdminArticleController:create');
  $app->get('/articles', '\AdminArticleController:showNews');
  $app->get('/article/{id}', '\AdminArticleController:show');
  $app->post('/article', '\AdminArticleController:store');
  $app->put('/article/{id}', '\AdminArticleController:update');
  $app->delete('/article/{id}', '\AdminArticleController:delete');
  $app->get('/articles/search', '\AdminArticleController:searchArticle');

  // Blog
  $app->get('/blog/new', '\AdminBlogController:create');
  $app->get('/blogs', '\AdminBlogController:showNews');
  $app->get('/blog/{id}', '\AdminBlogController:show');
  $app->post('/blog', '\AdminBlogController:store');
  $app->put('/blog/{id}', '\AdminBlogController:update');
  $app->delete('/blog/{id}', '\AdminBlogController:delete');
  $app->get('/blogs/search', '\AdminBlogController:searchBlog');

  // Page
  $app->get('/page/new', '\AdminPageController:create');
  $app->get('/pages', '\AdminPageController:showNews');
  $app->get('/page/{id}', '\AdminPageController:show');
  $app->post('/page', '\AdminPageController:store');
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
  $app->get('/menu/new', '\AdminMenuController:create');
  $app->get('/menus/{id}', '\AdminMenuController:show');
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
  $app->get('/api/create-handle', '\FunctionController:createHandle');
  $app->post('/api/create-handle-product', '\FunctionController:createHandleCollection');
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
