<?php
require_once(ROOT . '/models/Product.php');
require_once(ROOT . '/models/Collection.php');
require_once("../models/CollectionProduct.php");
require_once(ROOT . '/controllers/helper.php');
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Connection as DB;
use ControllerHelper as Helper;

class AdminProductController extends AdminController {

  public function index(Request $request, Response $response) {
    $data = Product::all();
    return $this->view->render($response, 'admin/product.pug', [
      'data' => $data
    ]);
  }

  public function create(Request $request, Response $response) {
    $collections = Collection::orderBy('breadcrumb', 'asc')->get();
    return $this->view->render($response, 'admin/product_new.pug', array(
      'collections' => $collections
    ));
  }

  public function show(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $data = Product::find($id);
    if(!$data) {
      $this->view->render($response, '404.jade');
      return $response->withStatus(404);
    }
    $collections = Collection::orderBy('breadcrumb', 'asc')->get();
    $variants = Variant::where('product_id', $id)->get();
    foreach ($variants as $key => $variant) {
      $variant->list_image = Image::getImage('variant', $variant->id);
    }
    $data->variants = $variants;
    $data->collection_id = CollectionProduct::where('product_id', $id)->get();
    return $this->view->render($response, 'admin/product_edit.pug', array(
      'data' => $data,
      'collections' => $collections
    ));
  }

  public function store (Request $request, Response $response) {
    $body = $request->getParsedBody();
    $arr = [
      'title' => $body['title']
    ];
    $checkNull = Helper::checkNull($arr);
    if ($checkNull) {
      return $response->withJson($checkNull, 200);
    }
    $code = Product::store($body);
    if ($code) {
      $collections = $body['collections'];
      foreach ($collections as $key => $collection) {
        CollectionProduct::store($collection, $code);
      }
    }
    $result = Helper::response($code);
    return $response->withJson($result, 200);
  }

  public function update(Request $request, Response $response) {
    try {
      $id = $request->getAttribute('id');
      $body = $request->getParsedBody();
      $data = Product::find($id);
      if (!$data) {
        $result = Helper::response(-2);
        return $response->withJson($result, 200);
      }
      $data->title = $body['title'];
      $data->handle = createHandle($body['title']);
      $data->featured_image = '';
      $data->description = $body['description'];
      $data->meta_description = $body['meta_description'];
      $data->meta_robots = $body['meta_robots'];
      $data->display = $body['display'];
      $data->inventory_management = $body['inventory_management'];
      $data->updated_at = date('Y-m-d H:i:s');
      $data->save();

      CollectionProduct::where('product_id', $id)->delete();
      $collections = $body['collections'];
      foreach ($collections as $key => $collection) {
        CollectionProduct::store($collection, $id);
      }

      $result = Helper::response(0);
      return $response->withJson($result, 200);

    } catch (Exception $e) {
      $result = Helper::response(-3);
      return $response->withJson($result, 200);
    }
  }

  public function delete(Request $request, Response $response) {
    try {
      $id = $request->getAttribute('id');
      $data = Product::find($id);
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

  public function updateFeaturedImage(Request $request, Response $response) {
    try {
      $id = $request->getAttribute('id');
      $body = $request->getParsedBody();
      $data = Product::find($id);
      if (!$data) {
        $result = Helper::response(-2);
        return $response->withJson($result, 200);
      }
      $data->featured_image = $body['featured_image'];
      $data->save();
      updateStock($id);
      $result = Helper::response(0);
      return $response->withJson($result, 200);
    } catch (Exception $e) {
      $result = Helper::response(-3);
      return $response->withJson($result, 200);
    }
  }
}

?>
