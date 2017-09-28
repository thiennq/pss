<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Product.php");
require_once("../models/Collection.php");
require_once("../framework/Sitemap.php");

class FunctionController extends Controller {

  public function createHandle(Request $request, Response $response) {
    $query = $request->getQueryParams();
    if($query['q']) {
      $str = $query['q'];
      $handle = $this->convertHandle($str);
      $handle = $this->checkHandle($handle);
      return $handle;
    }
    return 0;
  }

  public function convertHandle($str) {
    $str = trim($str);
    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
    $str = preg_replace("/(đ)/", 'd', $str);
    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
    $str = preg_replace("/(Đ)/", 'D', $str);
    $str = str_replace(' ', '-', $str);
    $str = str_replace('.', '-', $str);
    $str = strtolower($str);
    $str = preg_replace('/[^A-Za-z0-9-]+/', '-', $str);
    $str = str_replace('--', '-', $str);
    $str = str_replace('--', '-', $str);
    if(substr($str, -1) == '-') $str = substr($str, 0, -1);
    return $str;
  }

  public function checkHandle($handle) {
    $product = Product::where('handle', $handle)->count();
    $collection = Collection::where('link', $handle)->count();
    $article = Article::where('handle', $handle)->count();
    if($product || $collection || $article) return 0;
    return $handle;
  }

  public function createHandleCollection(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $title = $body['title'];
    $parent_id = $body['parent_id'];
    $handle = $this->convertHandle($title);
    $link = $handle;
    if($parent_id != -1) {
      $collection = Collection::find($parent_id);
      $link = $collection->$link . '/' . $handle;
    }
    $check = Collection::where('link', $link)->where('title', '!=', $title)->count();
    if($check) return 0;
    return $handle;
  }

  public function createHandleProduct(Request $request, Response $response) {
    $body = $request->getParsedBody();
    $title = $body['title'];
    $handle = $this->convertHandle($title);
    $check = Product::where('title', '!=', $title)->where('handle', $handle)->count();
    if($check) return 0;
    return $handle;
  }

  public function rotateImage(Request $request, Response $response) {
    $query = $request->getQueryParams();
    $file = $query['filename'];
    $path = ROOT . '/public/uploads/';
		$input = $path . $file;
		rotate($input);

    global $size;
    for ($i=0; $i < count($size); $i++) {
      $img = convertImage($file, $size[$i]);
  		rotate($path.$img);
    }
		return convertImage($file, $size[0]);
  }

  public function SiteMap() {
    $path = ROOT . '/public/';
    $sitemap = new Sitemap(HOST);
    $filename = 'sitemap_products_1';
    unlink($path . $filename . 'xml');
    $sitemap->setPath($path);
    $sitemap->setFilename($filename);
    $sitemap->addItem('/' , '1.0', 'Daily');
    $Product = Product::where('display', 1)->get();
    foreach ($Product as $key => $product) {
      $sitemap->addItem('/san-pham/' . $product['handle'] , '1.0', 'Daily', $product['updated_at']);
    }
    $sitemap->createSitemapIndex($path, 'Today');
    unlink($path . $filename . '-index.xml');




    $sitemap = new Sitemap(HOST);
    $filename = 'sitemap_collections_1';
    unlink($path . $filename . 'xml');
    $sitemap->setPath($path);
    $sitemap->setFilename($filename);
    $sitemap->addItem('/' , '1.0', 'Daily');
    $collections = Collection::all();
    foreach ($collections as $key => $collection) {
      $link = str_replace(',', '/', $collection['link']);
      $sitemap->addItem('/' . $link, '0.9', 'Daily', $collection['updated_at']);
    }
    $sitemap->createSitemapIndex($path, 'Today');
    unlink($path . $filename . '-index.xml');



    $sitemap = new Sitemap(HOST);
    $filename = 'sitemap_brands_1';
    unlink($path . $filename . 'xml');
    $sitemap->setPath($path);
    $sitemap->setFilename($filename);
    $sitemap->addItem('/' , '1.0', 'Daily');
    $brands = Brand::join('product', 'product.brand', '=', 'brand.name')->where('product.display', 1)->where('product.price', '>', 0)->groupBy('brand.name')->orderBy('brand.name', 'asc')->select('brand.*')->get();
    foreach ($brands as $key => $brand) {
      $sitemap->addItem('/thuong-hieu/' . $brand['handle'], '0.9', 'Daily', $brand['updated_at']);
    }
    $sitemap->createSitemapIndex($path, 'Today');
    unlink($path . $filename . '-index.xml');





    $sitemap = new Sitemap(HOST);
    $filename = 'sitemap_news_1';
    unlink($path . $filename . 'xml');
    $sitemap->setPath($path);
    $sitemap->setFilename($filename);
    $sitemap->addItem('/' , '1.0', 'Daily');
    $articles = Article::where('type', 'tin-tuc')->get();
    foreach ($articles as $key => $article) {
      $sitemap->addItem('/tin-tuc/' . $article['handle'] . '-'. $article['id'], '0.9', 'Daily', $article['updated_at']);
    }
    $sitemap->createSitemapIndex($path, 'Today');
    unlink($path . $filename . '-index.xml');

  }

  public function removeOneImage(Request $request, Response $response) {
    $query = $request->getQueryParams();
    $image = $query['name'];
    removeImage($image);
    return 'Deleted';
  }

  public function getSubRegion(Request $request, Response $response) {
    $query = $request->getQueryParams();
    $region_id = $query['region_id'];
    $subregion = SubRegion::where('region_id', $region_id)->orderBy('name', 'asc')->get();
    $price_suburban = Meta::where('key', 'price_suburban')->first();
    $price_suburban = (int) $price_suburban->value;
    $price_urban = Meta::where('key', 'price_urban')->first();
    $price_urban = (int) $price_urban->value;
    foreach ($subregion as $key => $value) {
      $value->shipping_price = $price_suburban;
      if($value->urban) $value->shipping_price = $price_urban;
    }
    return $response->withJson(array(
      'code' => 0,
      'data' => $subregion
    ));
  }

  public function initDB() {
    // Create user
    $obj = new stdClass();
    $obj->fullname = 'Admin';
    $obj->email = 'admin@gmail.com';
    $obj->phone = '0123456789';
    $obj->role = 'admin';
    $obj->password = 'admin';
    User::store($obj);
  }


}

?>
