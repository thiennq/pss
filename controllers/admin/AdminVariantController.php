<?php
require_once(ROOT . '/models/Product.php');
require_once(ROOT . '/models/Variant.php');
require_once(ROOT . '/models/Image.php');
require_once(ROOT . '/controllers/helper.php');
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use ControllerHelper as Helper;

class AdminVariantController extends AdminController {

  public function store (Request $request, Response $response) {
    $body = $request->getParsedBody();
    $arr = [
      'product_id' => $body['product_id'],
      'title' => $body['title'],
      'price' => $body['price'],
      'inventory' => $body['inventory']
    ];
    $checkNull = Helper::checkNull($arr);
    if ($checkNull) {
      return $response->withJson($checkNull, 200);
    }
    $code = Variant::store($body);
    if ($code) {
      $list_image = $body['list_image'];
      foreach ($list_image as $key => $image) {
        Image::store($image, 'variant', $code);
      }
    }
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function update(Request $request, Response $response) {
    try {
      $id = $request->getAttribute('id');
      $body = $request->getParsedBody();
      $variant = Variant::find($id);
      
      $code = Variant::update($id, $body);
      if (!$code) {
        $list_image = $body['list_image'];
        foreach ($list_image as $key => $image) {
          Image::store($image, 'variant', $id);
        }
        foreach ($body['image_deleted'] as $key => $value) {
          Image::removeImage($value, $id);
          Image::find($value)->delete();
        }
      }
      $result = Helper::response($code);
      return $response->withJson($result, 200);

    } catch (Exception $e) {
      $result = Helper::response(-3);
      return $response->withJson($result, 200);
    }
  }

  public function delete(Request $request, Response $response) {
    try {
      $id = $request->getAttribute('id');
      $data = Variant::find($id);
      if (!$data) {
        $result = Helper::response(-2);
        return $response->withJson($result, 200);
      }
      $data->delete();
      $result = Helper::response(0);
      return $response->withJson($result, 200);
    } catch (Exception $e) {
      $result = Helper::response(-3);
      return $response->withJson($result, 200);
    }
  }

  public function renderImageTinymce(Request $request, Response $response) {
    $dir = ROOT . '/public/images';
    $images = scandir($dir);
    array_shift($images);
    array_shift($images);
    return $this->view->render($response, 'admin/tinymce-upload.pug', array(
      "title" => "Upload image",
      "images" => $images,
      "total" => count($images)
    ));
  }
}

?>
