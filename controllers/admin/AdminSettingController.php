<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Meta.php");

class AdminSettingController extends AdminController {

  public function getWebsiteSetting(Request $request, Response $response) {
    $collections = Collection::orderBy('breadcrumb', 'asc')->get();
    return $this->view->render($response, 'admin/setting_website.pug', array(
      'collections' => $collections,
      'hotline1' => getMeta('hotline1'),
      'hotline2' => getMeta('hotline2'),
      'shop_name' => getMeta('shop_name'),
      'shop_address' => getMeta('shop_address'),
      'free_shipping' => getMeta('free_shipping'),
      'price_urban' => getMeta('price_urban'),
      'price_suburban' => getMeta('price_suburban'),
      'index_collection_id_1' => getMeta('index_collection_id_1'),
      'index_collection_id_2' => getMeta('index_collection_id_2'),
      'index_collection_id_3' => getMeta('index_collection_id_3'),
      'index_collection_title_1' => getMeta('index_collection_title_1'),
      'index_collection_title_2' => getMeta('index_collection_title_2'),
      'index_collection_title_3' => getMeta('index_collection_title_3'),
      'livechat' => getMeta('livechat')
    ));
  }

  public function updateWebsiteSetting(Request $request, Response $response) {
    $body = $request->getParsedBody();
    Meta::store('hotline1', $body['hotline1']);
    Meta::store('hotline2', $body['hotline2']);
    Meta::store('shop_name', $body['shop_name']);
    Meta::store('shop_address', $body['shop_address']);
    Meta::store('free_shipping', $body['free_shipping']);
    Meta::store('price_urban', $body['price_urban']);
    Meta::store('price_suburban', $body['price_suburban']);
    Meta::store('index_collection_id_1', $body['index_collection_id_1']);
    Meta::store('index_collection_id_2', $body['index_collection_id_2']);
    Meta::store('index_collection_id_3', $body['index_collection_id_3']);
    Meta::store('index_collection_title_1', $body['index_collection_title_1']);
    Meta::store('index_collection_title_2', $body['index_collection_title_2']);
    Meta::store('index_collection_title_3', $body['index_collection_title_3']);
    Meta::store('livechat', $body['livechat']);
    return $response->withJson(array(
      'code' => 0,
      'message' => 'Updated'
    ));
  }

  public function getSEOSetting(Request $request, Response $response) {
    return $this->view->render($response, 'admin/setting_seo.pug', array(
      'meta_title_default' => getMeta('meta_title_default'),
      'meta_description_default' => getMeta('meta_description_default'),
      'facebook_pixel' => getMeta('facebook_pixel'),
      'facebook_image' => getMeta('facebook_image')
    ));
  }

  public function updateSEOSetting(Request $request, Response $response) {
    $body = $request->getParsedBody();
    Meta::store('meta_title_default', $body['meta_title_default']);
    Meta::store('meta_description_default', $body['meta_description_default']);
    Meta::store('facebook_pixel', $body['facebook_pixel']);
    Meta::store('facebook_image', $body['facebook_image']);
    return $response->withJson(array(
      'code' => 0,
      'message' => 'Updated'
    ));
  }

  public function getImages(Request $request, Response $response) {
    $dir = ROOT . '/public/images';
    $images = scandir($dir);
    array_shift($images);
    array_shift($images);
    return $this->view->render($response, 'admin/images.pug', array(
      "images" => $images,
      "total" => count($images)
		));
  }

  public function removeImage(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $dir = ROOT . '/public/images/';
    $src = $dir . $body['img'];
    if(unlink($src)) {
      return $response->withJson([
        'code' => 0,
        'message' => 'Deleted'
      ]);
    }
    return $response->withJson([
      'code' => -1,
      'message' => 'Erorr'
    ]);
  }
}
?>
