<?php
session_start();

function currentENV() {
  $env = 'development';
  if (getenv('ENV') && getenv('ENV') == 'production') {
    $env = 'production';
  }
  return $env;
}

function getThemeDir() {
  return $GLOBALS['config']['themeDir'];
}

function themeURI() {
  return '/themes/' . $GLOBALS['config']['themeDir'] ;
}

function staticURI() {
  return '/static';
}


function getMeta($key) {
  $meta = Meta::where('key', $key)->first();
  return $meta->value;
}

function inInventory($productId) {
  $inventory = Variant::where('product_id', $productId)->select('inventory')->get();
  foreach ($inventory as $key => $value) {
    if ($value->inventory > 0) return 1;
  }
  return 0;
}

function listArticles($blogId, $pageNumber) {
  $perPage = 2;
  $skip = ($pageNumber - 1) * $perPage;
  $countArticles = Article::join('blog_article', 'article.id', '=', 'blog_article.article_id')->where('blog_article.blog_id', $blogId)->count();

  $articles = Article::join('blog_article', 'article.id', '=', 'blog_article.article_id')->where('blog_article.blog_id', $blogId)->skip($skip)->take($perPage)->orderBy('article.updated_at', 'desc')->select('article.*')->get();

  $totalPages = ceil($countArticles/$perPage);
  $articles->total_pages = $totalPages;
  return $articles;
}

function getHotArticle($id) {
  $data = Article::where('id', '!=', $id)->where('display', 1)->orderBy('view', 'desc')->orderBy('updated_at', 'desc')->take(5)->get();
  return $data;
}

function getRelatedArticle($articleId) {
  $blogId = Article::join('blog_article', 'article.id', '=', 'blog_article.article_id')->where('blog_article.article_id', $articleId)->first();
  $related_article = Article::join('blog_article', 'article.id', '=', 'blog_article.article_id')->where('blog_article.blog_id', $blogId->blog_id)->where('blog_article.article_id','!=', $articleId)->get();
  return $related_article;
}

function menu() {
  if(getMemcached('menus')) return getMemcached('menus');
  $data = Menu::fetch();
  setMemcached("menus", $data);
  return $data;
}

function ddMMYYYY($datetime) {
  return date("d-m-Y", strtotime($datetime));
}

function getPriceFilter() {
  $price = Price::all();
  $arr = array();
  for ($i=0; $i < count($price) ; $i++) {
    $obj = new stdClass();
    if(!$i) {
      $obj->title = 'Dưới ' . money($price[$i]['price']);
      $obj->value = '(<' . $price[$i]['price'] . ')';
    } else if($i == count($price) - 1) {
      $obj->title = 'Trên ' . money($price[$i]['price']);
      $obj->value = '(>' . $price[$i]['price'] . ')';
    } else {
      $obj->title = 'Từ ' . money($price[$i]['price']) . ' - ' . money($price[$i+1]['price']);
      $obj->value = '(' . $price[$i]['price'] . '-'.$price[$i+1]['price'] . ')';
    }
    array_push($arr, $obj);
  }
  return $arr;
}

function hotline1() {
  return getMeta('hotline1');
}

function hotline2() {
  return getMeta('hotline2');
}

function meta_title_default() {
  return getMeta('meta_title_default');
}

function meta_description_default() {
  return getMeta('meta_description_default');
}

function facebook_pixel() {
  return getMeta('facebook_pixel');
}

function facebook_image() {
  return getMeta('facebook_image');
}

function countArr($arr) {
  return count($arr);
}

function money($money) {
  if($money) return number_format($money) . 'đ';
  return 0;
}

function name() {
  $name = $_SESSION["name"];
  return $name;
}

function role() {
  return $_SESSION['role'];
}

function currentHost() {
  global $HOST;
  return $HOST;
}

function currentUrl() {
  $link = $_SERVER['REQUEST_URI'];
  return $link;
}

function resize($image, $value) {
  $arr = explode('.', $image);
  $extension = end($arr);
  $new_image = str_replace('.'.$extension, '_'. $value .'.'.$extension, $image);
  return $new_image;
}

function concatString($str1, $str2) {
  return $str1 . $str2;
}

function getPathname($url) {
  $index = strpos($url, '?');
  if($index !== false) {
    $url = substr($url, 0, $index);
  }
  return $url;
}


function canonical($page=null, $total=null) {
  if(strpos(currentUrl(), 'brand=') || strpos(currentUrl(), 'color=') || strpos(currentUrl(), 'price=') || strpos(currentUrl(), 'orderBy=')) {
    $text = '<link rel="canonical" href="'. getPathname(HOST . currentUrl()).'"/>';
  } else {
    $text = '<link rel="canonical" href="'. HOST . currentUrl().'"/>';
    if($page == 1 && $total > 1) $text = $text . '<link rel="next" href="'. HOST .currentUrl().'?page=2"/>';
    else if($page == $total && $total == 2) {
      $text = $text . '<meta name="robots" content="noindex,follow"/>';
      $text = $text . '<link rel="prev" href="'. getPathname(HOST . currentUrl()).'"/>';
    } else if($page < $total && $total > 2){
      $text = $text . '<meta name="robots" content="noindex,follow"/>';
      $prev = $page - 1;
      $next = $page + 1;
      $text = $text . '<link rel="prev" href="'. getPathname(HOST . currentUrl()). '?page='. $prev .'"/>';
      $text = $text . '<link rel="next" href="'. getPathname(HOST . currentUrl()). '?page='. $next .'"/>';
    } else if($page == $total && $total > 2) {
      $text = $text . '<meta name="robots" content="noindex,follow"/>';
      $prev = $page - 1;
      $text = $text . '<link rel="prev" href="'. getPathname(HOST . currentUrl()). '?page='. $prev .'"/>';
    }
  }
  return $text;
}

function fullUrl($link=null) {
  if(isset($link)) return HOST . '/' . $link;
  return HOST;
}

function livechat() {
  return getMeta('livechat');
}

function slider() {
  if(getMemcached('slider')) return getMemcached('slider');
  $data = Slider::where('display', 1)->get();
  setMemcached("slider", $data);
  return $data;
}

function collectionIndex() {
  if(getMemcached('productIndex')) return getMemcached('productIndex');
  $data = array();
  for ($i=1; $i < 4; $i++) {
    $obj = new stdClass();
    $obj->title = getMeta('index_collection_title_' . $i);
    $collection_id = getMeta('index_collection_id_' . $i);
    $products = Product::Join('collection_product', 'collection_product.product_id', '=', 'product.id')
      ->where('collection_product.collection_id', $collection_id)
      ->where('product.display', 1)->where('product.in_stock', 1)
      ->orderBy('product.in_stock', 'desc')->orderby('product.updated_at', 'desc')
      ->select('product.*')->take(5)->get();
    $products = Product::getInfoProduct($products);
    $obj->products = $products;
    array_push($data, $obj);
  }
  setMemcached("productIndex", $data);
  return $data;
}

function articleIndex() {
  if(getMemcached('articleIndex')) return getMemcached('articleIndex');
  $articles = Article::where('display', 1)->where('type', 'tin-tuc')->orderby('updated_at', 'desc')->take(4)->get();
  setMemcached("articleIndex", $articles);
  return $articles;
}
