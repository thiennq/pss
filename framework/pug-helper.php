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

function listArticles($blogId, $pageNumber) {
  $perPage = 2;
  $skip = ($pageNumber - 1) * $perPage;
  $countArticles = Article::join('blog_article', 'article.id', '=', 'blog_article.article_id')->where('blog_article.blog_id', $blogId)->count();

  $articles = Article::join('blog_article', 'article.id', '=', 'blog_article.article_id')->where('blog_article.blog_id', $blogId)->skip($skip)->take($perPage)->orderBy('article.updated_at', 'desc')->select('article.*')->get();

  $totalPages = ceil($countArticles/$perPage);
  $articles->total_pages = $totalPages;
  return $articles;
}

function getArticleDetail($articleHandle, $articleId) {
  $responseData = array();
  if(getMemcached('article_' . $articleHandle)) $responseData =  json_decode(getMemcached('article_' . $articleHandle), true);
  else {
    $hot_article = Article::where('id', '!=', $article->id)->where('display', 1)->orderBy('view', 'desc')->orderBy('updated_at', 'desc')->take(5)->get();

    $related = array();
    $arr_related = ArticleRelated::where('article_id', $id)->get();
    // foreach ($arr_related as $key => $value) {
    //   $item = Article::find($value->article_related);
    //   array_push($related, $item);
    // }

    $responseData = array(
      'hot_article' => $hot_article,
      'related' => $related
    );
    return $responseData;
  }
}

function Menu() {
  if(getMemcached('menus')) $menus = getMemcached('menus');
  else {
    $menus = Menu::getMenu();
    // setMemcached("menus", $menus);
  }
  return $menus;
}

function menuSidebarCollection() {
  $menu = Menu::menuSidebarCollection();
  return $menu;
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

function sale_policy() {
  return getMeta('sale_policy');
}

function hotline1() {
  return getMeta('hotline1');
}

function hotline2() {
  return getMeta('hotline2');
}
function policy() {
  return getMeta('footer1');
}
function branchs() {
  return getMeta('footer2');
}

function banner_shopping_footer() {
  return getMeta('banner_shopping_footer');
}

function banner_complain_footer() {
  return getMeta('banner_complain_footer');
}

function Brand() {
  $list_brand = [];
  $brands = Brand::join('product', 'product.brand', '=', 'brand.name')->where('product.display', 1)->where('product.price', '>', 0)->groupBy('brand.name')->select('brand.handle as handle', 'brand.name as name')->get()->toArray();
  $count_brand = count($brands);
  $floor = floor($count_brand / 7);
  $fmod = fmod($count_brand, 7);
  $skip = 0;
  $temp = 1;
  for ($i=0; $i<7; $i++) {
    $perpage = $floor;
    $skip = $i * $floor;
    $take = $floor;
    if($fmod) {
      $take = $take + 1;
      $fmod--;
      if($i) {
        $skip = $skip + $temp;
        $temp++;
      }
    } else $skip = $skip + $temp;
    $brand = Brand::join('product', 'product.brand', '=', 'brand.name')->where('product.display', 1)->where('product.price', '>', 0)->select('brand.handle as handle', 'brand.name as name')->skip($skip)->take($take)->groupBy('brand.name')->get();
    array_push($list_brand, $brand);
  }
  return $list_brand;
}

function BrandatIndexPage() {
  $list_brand = [];
  $brands = Brand::join('product', 'product.brand', '=', 'brand.name')->where('product.display', 1)->where('product.price', '>', 0)->groupBy('brand.name')->select('brand.handle as handle', 'brand.name as name')->get()->toArray();
  $count_brand = count($brands);
  $floor = floor($count_brand / 4);
  $fmod = fmod($count_brand, 4);
  $skip = 0;
  $temp = 1;
  for ($i=0; $i<4; $i++) {
    $perpage = $floor;
    $skip = $i * $floor;
    $take = $floor;
    if($fmod) {
      $take = $take + 1;
      $fmod--;
      if($i) {
        $skip = $skip + $temp;
        $temp++;
      }
    } else $skip = $skip + $temp;
    $brand = Brand::join('product', 'product.brand', '=', 'brand.name')->where('product.display', 1)->where('product.price', '>', 0)->select('brand.handle as handle', 'brand.name as name')->skip($skip)->take($take)->groupBy('brand.name')->get();
    array_push($list_brand, $brand);
  }
  return $list_brand;
}

function countArr($arr) {
  return count($arr);
}

function money($money) {
  if($money) return number_format($money) . 'đ';
  return 0;
}

function _money($money) {
  if($money) return number_format($money);
  return 0;
}

function fullname() {
  $fullname = $_SESSION["fullname"];
  return $fullname;
}

function role() {
  $email = $_SESSION["email"];
  $role = Role::where('email', $email)->first();
  return $role;
}

function compareString($str1, $str2) {
  if ($str1 == $str2) return true;
  return false;
}

function get_text_discount() {
  $title = Meta::where('key', 'DISCOUNT_TEXT')->first();
  return $title['value'];
}

function get_text_discount_en() {
  $title_en = Meta::where('key', 'DISCOUNT_TEXT_EN')->first();
  return $title_en['value'];
}

function get_link_discount() {
  $link = Meta::where('key', 'DISCOUNT_LINK')->first();
  return $link['value'];
}

function get_link_discount_en() {
  $link_en = Meta::where('key', 'DISCOUNT_LINK_EN')->first();
  return $link_en['value'];
}

function get_google_analytics() {
  $google_analytics = Meta::where('key', 'GOOGLE_ANALYTICS')->first();
  return $google_analytics['value'];
}

function get_facebook_pixels() {
  $google_pixels = Meta::where('key', 'FACEBOOK_PIXELS')->first();
  return $google_pixels['value'];
}

function get_google_adwords($price) {
  $price = (float) $price - 30000;
  $google_adwords = Meta::where('key', 'GOOGLE_ADWORDS')->first();
  $google_adwords['value'] = str_replace('{{price}}', $price, $google_adwords['value']);
  return $google_adwords['value'];
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
  $livechat = Meta::where('key', 'livechat')->first();
  return $livechat->value;
}

function menu_mobile() {
  $menu_mobile = Meta::where('key', 'menu_mobile')->first();
  return $menu_mobile->value;
}

function banner_default_fb() {
  $banner = Slider::where('display', 1)->first();
  return HOST . '/uploads/' . $banner->image;
}

function PHPMailer($to, $subject, $body, $text) {
  $mail = new PHPMailer;
  include ROOT . '/framework/phpmailer.php';

  $mail->IsSMTP();
  $mail->Host = $STMP_HOST;
  $mail->SMTPAuth = true;
  $mail->Username = $STMP_USERNAME;
  $mail->Password = $STMP_PASSWORD;
  $mail->SMTPSecure = $STMP_SECURE;
  $mail->Port = $STMP_PORT;
  $mail->setFrom($STMP_USERNAME, 'Admin');
  $mail->AddAddress($to);
  $mail->isHTML(true);
  $mail->Subject = $subject;
  $mail->Body    = $body;
  $mail->AltBody = $text;
  $mail->CharSet = "UTF-8";
  $mail->FromName = "GYPSY";
  if(!$mail->send())  {
    $message = "SEND FAILED !!! To : " . $to . " . Subject : " . $subject . " Content : " . $body . " Text : " . $text;
    return $STMP_USERNAME;
  }
  $message = "SEND SUCCESS ! To : " . $to . " . Subject : " . $subject . " Content : " . $body . " Text : " . $text;
  return true;
}

// INDEX
function slider() {
  if(getMemcached('slider')) {
    $slider = getMemcached('slider');
  }
  else {
    $slider = Slider::where('display', 1)->get();
    // setMemcached("slider", $slider);
  }
  return $slider;
}

function brandIndex() {
  $test = Brand::where('display', 1)->get();
  if(getMemcached('brandIndex')) return getMemcached('brandIndex');
  $brand = Brand::where('display', 1)->where('highlight', 1)->take(8)->get();
  // setMemcached("brandIndex", $brand);
  return $brand;
}

function collectionIndex() {
  if(getMemcached('productIndex')) return getMemcached('productIndex');
  $data = array();
  for ($i=1; $i < 5; $i++) {
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
  // setMemcached("productIndex", $data);
  return $data;
}

function articleIndex() {
  if(getMemcached('articleIndex')) return getMemcached('articleIndex');
  $articles = Article::where('display', 1)->where('type', 'tin-tuc')->orderby('updated_at', 'desc')->take(4)->get();
  // setMemcached("articleIndex", $articles);
  return $articles;
}

function celebrityIndex() {
  if(getMemcached('celebrityIndex')) return getMemcached('celebrityIndex');
  $celebrities = Celebrity::where('display', 1)->orderby('id', 'desc')->get();
  // setMemcached("celebrityIndex", $celebrities);
  return $celebrities;
}


// END INDEX
