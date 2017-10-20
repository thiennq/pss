<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Product.php");
require_once("../models/Slider.php");
require_once("../models/CollectionProduct.php");
require_once("../models/helper.php");
require_once("../models/Collection.php");

class ProductController extends Controller {

  public function smartSearch(Request $request, Response $response) {
    $query = $request->getQueryParams();
    $products = Product::where('title', 'LIKE', '%'.$query['q'].'%')->where('display', 1)->where('price', '>', 0)->skip(0)->take(5)->orderBy('updated_at', 'desc')->get();
    if(count($products)) {
      return $response->withJson(array(
        "code" => 0,
        "data" => $products
      ));
    }
    return $response->withJson(array(
      "code" => -1,
      "message" => "Product not available"
    ));
  }

  public function show(Request $request, Response $response) {
    $handle = $request->getAttribute('handle');
    $query = $request->getQueryParams();
    $responseData = array();
    if(getMemcached('product_' . $handle)) $responseData =  json_decode(getMemcached('product_' . $handle), true);
    else {
      $product = Product::where('handle', $handle)->first();
      error_log('PRODUCT :: ' . $product);
      if(!$product) {
        $this->view->render($response, '404.pug');
        return $response->withStatus(404);
      }
      $variants = Variant::where('product_id', $product->id)->get();
      

      $featured_images = array();
      foreach ($variants as $key => $variant) {
        $image = Image::where('typeId', $variant->id)->first();
        array_push($featured_images, $image);
      }
      error_log('FIRST ::' . json_encode($featured_images));

      $list_images = Variant::join('image', 'variant.id', '=', 'image.typeId')->where('variant.product_id', $product->id)->get();
      error_log('IMAGES ::' . $list_images);

      // $product->display_discount = false;
      // if($product->price_compare && $product->price_compare > $product->price) {
      //   $product->discount = $product->price_compare - $product->price;
      //   $product->per = ($product->discount)/($product->price_compare) * 100;
      //   $product->percent = round($product->per, 0) .'%';
      //   $product->display_discount = true;
      // }

    $responseData = array (
      'data' => $product,
      'variants' => $variants,
      'featured_images' => $featured_images,
      'list_images' => $list_images
    );
  }

    return $this->view->render($response, 'product.pug', $responseData);
  }

  public function findProductModal(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $product = Product::find($id);
    if (!$product) return 'empty';

    $product->display_discount = 0;
    if($product->price_compare && $product->price_compare > $product->price) {
      $product->percent = 0;
      $product->discount = $product->price_compare - $product->price;
      $product->percent = ($product->discount / $product->price_compare) * 100;
      $product->percent = round($product->percent, 0) .'%';
      $product->display_discount = 1;
    }

    $product->in_stock = false;
    $arr_branch_display = array();
    $branch = Inventory::join('branch', 'branch.id', '=', 'inventory.branch_id')->where('branch.calc_inventory', 1)->where('inventory.product_id', $product->id)->where('inventory.inventory', '>', 0)->select('branch.*')->get();
    if(count($branch)) {
      $product->in_stock = true;
      foreach ($branch as $key => $value) {
        if(!$value->branch_center) {
          $obj = new stdClass();
          $obj->name = $value->name;
          $obj->address = $value->address;
          array_push($arr_branch_display, $obj);
        }
      }
    }
    $product->count_branch_display = count($arr_branch_display);
    $product->arr_branch_display = $arr_branch_display;
    $collection_parent = CollectionProduct::where('product_id', $product->id)->join('collection', 'collection.id', '=', 'collection_product.collection_id')->where('collection.parent_id', '-1')->first();
    $collection_parent = Collection::find($collection_parent->id);
    $product->title = $collection_parent->title . ' ' . $product->title;
    $list_image = Image::getImage('product', $product->id);
    $product->list_image = $list_image;
    $product->brand_url = createHandle($product->brand);

    $product->count_variant = 0;
    if($product->group_id) {
      $product->variants = Product::where('group_id', $product->group_id)->where('display', 1)->get();
      $product->count_variant = count($product->variants);
    }

    if ($product->dropship) {
      if (!$product->in_stock) {
        $product->in_stock = true;
        $product->count_branch_display = 0;
      }
    }
    return $this->view->render($response, 'snippet/modal-order-data.pug', [
      'data' => $product
    ]);
  }

  public function findProductVariant(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $product = Product::find($id);
    if (!$product) {
      return $response->withJson([
        'code' => -1,
        'message' => 'not found'
      ]);
    }

    $product->display_discount = 0;
    if($product->price_compare && $product->price_compare > $product->price) {
      $product->percent = 0;
      $product->discount = $product->price_compare - $product->price;
      $product->percent = ($product->discount / $product->price_compare) * 100;
      $product->percent = round($product->percent, 0) .'%';
      $product->display_discount = 1;
    }

    $product->in_stock = false;
    $arr_branch_display = array();
    $branch = Inventory::join('branch', 'branch.id', '=', 'inventory.branch_id')->where('branch.calc_inventory', 1)->where('inventory.product_id', $product->id)->where('inventory.inventory', '>', 0)->select('branch.*')->get();
    if(count($branch)) {
      $product->in_stock = true;
      foreach ($branch as $key => $value) {
        if(!$value->branch_center) {
          $obj = new stdClass();
          $obj->name = $value->name;
          $obj->address = $value->address;
          array_push($arr_branch_display, $obj);
        }
      }
    }
    $product->count_branch_display = count($arr_branch_display);
    $product->arr_branch_display = $arr_branch_display;
    $collection_parent = CollectionProduct::where('product_id', $product->id)->join('collection', 'collection.id', '=', 'collection_product.collection_id')->where('collection.parent_id', '-1')->first();
    $collection_parent = Collection::find($collection_parent->id);
    $product->title = $collection_parent->title . ' ' . $product->title;
    $list_image = Image::getImage('product', $id);
    $product->list_image = $list_image;

    if ($product->dropship) {
      if (!$product->in_stock) {
        $product->in_stock = true;
        $product->count_branch_display = 0;
      }
    }
    return $response->withJson([
      'code' => 0,
      'data' => $product
    ]);
  }
}

?>
