<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Product.php");
require_once("../models/Slider.php");
require_once("../models/CollectionProduct.php");
require_once("../models/helper.php");
require_once("../models/Collection.php");

class ProductController extends Controller {

  public function show(Request $request, Response $response) {
    $handle = $request->getAttribute('handle');
    $query = $request->getQueryParams();

    $product = Product::where('handle', $handle)->first();
    if(!$product) {
      $this->view->render($response, '404');
      return $response->withStatus(404);
    }

    $variants = Variant::where('product_id', $product->id)->get();
    foreach ($variants as $key => $variant) {
      $image = Image::where('typeId', $variant->id)->first();
      $variant->image = $image->name;
    }

    $list_images = Variant::join('image', 'variant.id', '=', 'image.typeId')->where('variant.product_id', $product->id)->get();

    $product_collection = Product::join('collection_product', 'product.id', '=', 'collection_product.product_id')->where('collection_product.product_id', $product->id)->inRandomOrder()->first();
    $related_products = Product::join('collection_product', 'product.id', '=', 'collection_product.product_id')->where('collection_product.collection_id', $product_collection->collection_id)->where('product.id', '!=', $product->id)->select('product.*')->take(5)->get();

    if ($product->display) {
      if(isset($_SESSION['seen']) && !empty($_SESSION['seen'])) {
        if(!in_array($product->id, $_SESSION['seen'])) array_push($_SESSION['seen'], $product->id);
      } else $_SESSION['seen'] = [$product->id];
    }
    $GLOBALS['product_id'] = $product->id;
    if (count($_SESSION['seen'])) {
      $product_seen = Product::where('display', 1)->where('id', '!=', $product->id)->whereIn('id', $_SESSION['seen'])->take(5)->get();
    }

  $responseData = array (
    'data' => $product,
    'variants' => $variants,
    'list_images' => $list_images,
    'related_products' => $related_products,
    'product_seen' => $product_seen
  );

    return $this->view->render($response, 'product', $responseData);
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
    $collection_parent = CollectionProduct::where('product_id', $product->id)->join('collection', 'collection.id', '=', 'collection_product.collection_id')->where('collection.parent_id', '-1')->first();
    $collection_parent = Collection::find($collection_parent->id);
    $product->title = $collection_parent->title . ' ' . $product->title;
    $list_image = Image::getImage('product', $id);
    $product->list_image = $list_image;

    return $response->withJson([
      'code' => 0,
      'data' => $product
    ]);
  }
}

?>
