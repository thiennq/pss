<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Meta.php");

class AdminSettingController extends AdminController {

  public function settingIndex(Request $request, Response $response) {
    $collections = Collection::where('show_landing_page', 0)->orderBy('breadcrumb', 'asc')->get();
    return $this->view->render($response, 'admin/setting_index.pug', array(
      'collections' => $collections,
      'index_collection_id_1' => getMeta('index_collection_id_1'),
      'index_collection_id_2' => getMeta('index_collection_id_2'),
      'index_collection_id_3' => getMeta('index_collection_id_3'),
      'index_collection_id_4' => getMeta('index_collection_id_4'),
      'index_collection_title_1' => getMeta('index_collection_title_1'),
      'index_collection_title_2' => getMeta('index_collection_title_2'),
      'index_collection_title_3' => getMeta('index_collection_title_3'),
      'index_collection_title_4' => getMeta('index_collection_title_4')
    ));
  }

  public function updateSettingIndex(Request $request, Response $response) {
    $body = $request->getParsedBody();
    Meta::store('index_collection_id_1', $body['index_collection_id_1']);
    Meta::store('index_collection_id_2', $body['index_collection_id_2']);
    Meta::store('index_collection_id_3', $body['index_collection_id_3']);
    Meta::store('index_collection_id_4', $body['index_collection_id_4']);
    Meta::store('index_collection_title_1', $body['index_collection_title_1']);
    Meta::store('index_collection_title_2', $body['index_collection_title_2']);
    Meta::store('index_collection_title_3', $body['index_collection_title_3']);
    Meta::store('index_collection_title_4', $body['index_collection_title_4']);
    return $response->withJson(array(
      'code' => 0,
      'message' => 'Updated'
    ));
  }

  public function saveMeta(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $key = $body['key'];
    $value = $body['value'];
    Meta::store($key, $value);
    return $response->withJson(array(
      'code' => 0,
      'message' => 'Saved'
    ));
  }

  public function settingShipping(Request $request, Response $response) {
    $free_shipping = Meta::where('key', 'free_shipping')->first();
    $free_shipping = $free_shipping->value;
    $price_urban = Meta::where('key', 'price_urban')->first();
    $price_urban = $price_urban->value;
    $price_suburban = Meta::where('key', 'price_suburban')->first();
    $price_suburban = $price_suburban->value;
    return $this->view->render($response, 'admin/setting_shipping.pug', array(
      'free_shipping' => $free_shipping,
      'price_urban' => $price_urban,
      'price_suburban' => $price_suburban
    ));
  }

  public function updateSettingShiping(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $free_shipping = $body['free_shipping'];
    $price_urban = $body['price_urban'];
    $price_suburban = $body['price_suburban'];
    Meta::store('free_shipping', $free_shipping);
    Meta::store('price_urban', $price_urban);
    Meta::store('price_suburban', $price_suburban);
    return $response->withJson(array(
      'code' => 0,
      'message' => 'Updated'
    ));
  }

  public function getDesktopSetting(Request $request, Response $response) {
    return $this->view->render($response, 'admin/setting_desktop.pug', array(
      'hotline1' => getMeta('hotline1'),
      'hotline2' => getMeta('hotline2'),
			'sale_policy' => getMeta('sale_policy'),
      'footer1' => getMeta('footer1'),
      'footer2' => getMeta('footer2'),
      'banner_shopping_footer' => getMeta('banner_shopping_footer'),
      'banner_complain_footer' => getMeta('banner_complain_footer'),
      'banner_saleoff' => getMeta('banner_saleoff'),
    ));
  }

  public function updateDesktopSetting(Request $request, Response $response) {
    $body = $request->getParsedBody();
    Meta::store('hotline1', $body['hotline1']);
    Meta::store('hotline2', $body['hotline2']);
    Meta::store('sale_policy', $body['sale_policy']);
    Meta::store('footer1', $body['footer1']);
    Meta::store('footer2', $body['footer2']);
    Meta::store('banner_shopping_footer', $body['banner_shopping_footer']);
    Meta::store('banner_complain_footer', $body['banner_complain_footer']);
    Meta::store('banner_saleoff', $body['banner_saleoff']);
    return $response->withJson(array(
      'code' => 0,
      'message' => 'Updated'
    ));
  }

  public function getMetaTitleSetting(Request $request, Response $response) {
    return $this->view->render($response, 'admin/setting_meta.pug', array(
      'meta_title_default' => getMeta('meta_title_default'),
      'meta_description_default' => getMeta('meta_description_default'),
      'meta_title_new_product' => getMeta('meta_title_new_product'),
      'meta_description_new_product' => getMeta('meta_description_new_product'),
      'meta_title_saleoff' => getMeta('meta_title_saleoff'),
      'meta_description_saleoff' => getMeta('meta_description_saleoff'),
      'meta_description_product' => getMeta('meta_description_product')
    ));
  }

  public function updateMetaTitleSetting(Request $request, Response $response) {
    $body = $request->getParsedBody();
    Meta::store('meta_title_default', $body['meta_title_default']);
    Meta::store('meta_description_default', $body['meta_description_default']);
    Meta::store('meta_title_new_product', $body['meta_title_new_product']);
    Meta::store('meta_description_new_product', $body['meta_description_new_product']);
    Meta::store('meta_title_saleoff', $body['meta_title_saleoff']);
    Meta::store('meta_description_saleoff', $body['meta_description_saleoff']);
    Meta::store('meta_description_product', $body['meta_description_product']);
    return $response->withJson(array(
      'code' => 0,
      'message' => 'Updated'
    ));
  }

  public function getLiveChat(Request $request, Response $response) {
    return $this->view->render($response, 'admin/livechat.pug', array(
      'livechat' => getMeta('livechat')
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
    $dir = ROOT . '/public/uploads/images/';
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
