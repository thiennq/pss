<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Product.php");
require_once("../models/Collection.php");
require_once("../framework/Sitemap.php");

class FunctionController extends Controller {

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
