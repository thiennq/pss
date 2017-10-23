<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once('Product.php');
require_once('Collection.php');

$GLOBALS['size'] = [100, 240, 480, 640, 1024, 2048];


function setMemcached($key, $value, $time=30*24*60*60) {
  global $memcached;
  if ($memcached) {
    $memcached->set($key, $value, $time);
  }
}

function getMemcached($key) {
  global $memcached;
  if($memcached) return $memcached->get($key);
  return false;
}

function clearAllMemcached() {
  global $memcached;
  $memcached->flush();
}

function getTitleFromHandle($handle) {
  $obj = Collection::where('handle', '=', $handle)->first();
  if ($obj) return $obj['title'];
  $obj = Product::where('handle', '=', $handle)->first();
  if ($obj) return $obj['title'];
  return '';
}

function createHandle($str) {
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

function removeImage($name) {
  $path = ROOT . '/public/uploads/';
  unlink($path.$name);
  global $size;
  for ($i=0; $i < count($size); $i++) {
    $img = convertImage($name, $size[$i]);
    unlink($path.$img);
  }
}

function uploadImage(Request $request, Response $response) {
  $path = ROOT . '/public/uploads/';
  $result = array();
  $total = count($_FILES['upload']['name']);
  global $size;
  for($i=0; $i < $total; $i++) {
    $tmp_name = $_FILES['upload']['tmp_name'][$i];
    $origin = $_FILES['upload']['name'][$i];
    $newName = time() . '_' . $origin;
    $newFilePath = $path . $newName;
    if (moveAndReduceSize($tmp_name, $newFilePath, 70)) {
      array_push($result, $newName);
      for ($j=0; $j < count($size); $j++) {
        moveAndReduceSize($tmp_name, $newFilePath, 70, $size[$j]);
      }
      $img_2048 = convertImage($newFilePath, 2048);
      rename($img_2048, $newFilePath);
    }
  }
  if(count($result)) {
    return $response->withJson([
      'code' => 0,
      'data' => $result
    ]);
  }
  return $response->withJson([
    'code' => -1,
    'message' => 'error'
  ]);
}

function moveAndReduceSize($srcFilePath, $outFilePath, $quality, $size=NULL) {
  list($width, $height) = getimagesize($srcFilePath);
  if (isset($size) && $size) {
    $scale = min($size/$width, $size/$height);
    $newWidth = ceil($scale * $width);
    $newHeight = ceil($scale * $height);
    if ($width < $newWidth || $height < $newHeight) {
      $newWidth = $width;
      $newHeight = $height;
    }
    $outFilePath = convertImage($outFilePath, $size);
  } else {
    $newWidth = $width;
    $newHeight = $height;
  }
  $mime = mime_content_type($srcFilePath);
  $mime = strtolower($mime);
  $thumb = imagecreatetruecolor($newWidth, $newHeight);
  $support = true;
  if($mime == "image/jpeg") $source = imagecreatefromjpeg($srcFilePath);
  else if($mime == "image/gif") $source = imagecreatefromgif($srcFilePath);
  else if($mime == "image/png") {
    $source = imagecreatefrompng($srcFilePath);
    imagealphablending($thumb, false);
    imagesavealpha($thumb,true);
    $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
    imagefilledrectangle($thumb, 0, 0, $newWidth, $newHeight, $transparent);
  } else $support = false;
  if($support) {
    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    if($mime == "image/jpeg") imagejpeg($thumb, $outFilePath, $quality);
    else if($mime == "image/png") imagepng($thumb, $outFilePath, floor(($quality -1) / 10));
    else if($mime == "image/gif") imagegif($thumb, $outFilePath);
  }
  return true;
}

function convertImage($file, $size) {
  $temp = explode('.', $file);
  $extension = end($temp);
  $new = str_replace('.'.$extension, '_'. $size .'.'.$extension, $file);
  return $new;
}

function uploadImageTinymce(Request $request, Response $response) {
  $result = array();
  $total = count($_FILES['upload']['name']);
  for($i=0; $i<$total; $i++) {
    $tmp_name = $_FILES['upload']['tmp_name'][$i];
    $new_name = time().'-'.$_FILES['upload']['name'][$i];
    $path = ROOT . '/public/images/'.$new_name;
    if ($tmp_name != ""){
      if(move_uploaded_file($tmp_name, $path)) {
        array_push($result, $new_name);
      }
    }
  }
  if($result) {
    return $response->withJson(array(
      'code' => 0,
      'data' => $result
    ));
  }
  return $response->withJson(array(
    'code' => -1,
    'message' => 'Error'
  ));
}

function renameOneImage($image, $handle) {
  $arr = explode('.', $image);
  $ext = end($arr);
  $name = str_replace('.'.$ext, '', $image);
  $path = ROOT . '/public/uploads/';
  rename($path.$name.'.'.$ext, $path.$handle.'.'.$ext);
  global $size;
  for ($i=0; $i < count($size); $i++) {
    rename($path.$name.'_'.$size[$i].'.'.$ext, $path.$handle.'_'.$size[$i].'.'.$ext);
  }
  return $handle.'.'.$ext;
}

function renameListImage($list_image, $handle) {
  $new_list_image = array();
  $count = 0;
  foreach ($list_image as $key => $image) {
    if($image) {
      $arr = explode('.', $image);
      $ext = end($arr);
      $name = str_replace('.'.$ext, '', $image);
      $path = ROOT . '/public/uploads/';
      $count++;
      global $size;
      if($count > 1) {
        rename($path.$name.'.'.$ext, $path.$handle.'-' . $count.'.'.$ext);
        for ($i=0; $i < count($size); $i++) {
          rename($path.$name.'_'.$size[$i].'.'.$ext, $path.$handle.'-' . $count.'_'.$size[$i].'.'.$ext);
        }
        array_push($new_list_image, $handle . '-' . $count . '.' . $ext);
      } else {
        rename($path.$name.'.'.$ext, $path.$handle.'.'.$ext);
        for ($i=0; $i < count($size); $i++) {
          rename($path.$name.'_'.$size[$i].'.'.$ext, $path.$handle.'_'.$size[$i].'.'.$ext);
        }
        array_push($new_list_image, $handle. '.' . $ext);
      }
    }
  }
  return $new_list_image;
}

function rotate($input) {
  header('Content-type: image/jpeg');
  if(file_exists($input)) {
    try {
      $source = imagecreatefromjpeg($input);
      $degrees = 90;
      $rotate = imagerotate($source, $degrees, 0);
      imagejpeg($rotate, $input);
    } catch (Exception $e) {
    }
  }
  imagedestroy($source);
  imagedestroy($rotate);
}

function SiteMap() {
  $path = ROOT . '/public/';
  $sitemap = new Sitemap(HOST);
  $filename = 'sitemap_products_1';
  unlink($path . $filename . 'xml');
  $sitemap->setPath($path);
  $sitemap->setFilename($filename);
  $sitemap->addItem('/' , '1.0', 'Daily');
  $Product = Product::where('display', 1)->orderBy('updated_at', 'desc')->get();
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
  $filename = 'sitemap_news_1';
  unlink($path . $filename . 'xml');
  $sitemap->setPath($path);
  $sitemap->setFilename($filename);
  $sitemap->addItem('/' , '1.0', 'Daily');
  $articles = Article::where('display', 1)->get();
  foreach ($articles as $key => $article) {
    $sitemap->addItem('/article/' . $article['handle'] . '-'. $article['id'], '0.9', 'Daily', $article['updated_at']);
  }
  $sitemap->createSitemapIndex($path, 'Today');
  unlink($path . $filename . '-index.xml');
}

function createSitemap() {
  SiteMap();
  global $HOST;
  $domtree = new DOMDocument('1.0', 'UTF-8');
  $domtree->preserveWhiteSpace = false;
  $domtree->formatOutput = true;
  /* Create attribute */
  $domAttribute = $domtree->createAttribute('xmlns');
  /* Value for the created attribute */
  $domAttribute->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';

  /* Create the root element of the xml tree */
  $xmlRoot = $domtree->createElement("sitemapindex");
  $xmlRoot->appendChild($domAttribute);
  /* Append it to the document created */
  $xmlRoot = $domtree->appendChild($xmlRoot);

  $sitemaps = array('products','collections','news');
  $prefix = '/sitemap_';
  $extension = '_1.xml';
  foreach ($sitemaps as $key => $sitemap) {
    $currentSitemap = $domtree->createElement("sitemap");
    $currentSitemap = $xmlRoot->appendChild($currentSitemap);
    $currentSitemap->appendChild($domtree->createElement('loc', $HOST . $prefix . $sitemap . $extension));
  }

  $xml_out = $domtree->saveXML($domtree->documentElement);
  $file = fopen(ROOT . '/public/sitemap.xml',"w");
  if (fwrite($file, $xml_out) !== FALSE) {
    $link = HOST . '/sitemap.xml';
    echo "Success: <a href=".$link.">".$link."</a>" ;
  } else echo "An error occured, please try again later";
  fclose($file);
}

function updateStock($product_id) {
  $product = Product::find($product_id);
  if (!$product->inventory_management) {
    $product->in_stock = 1;
  }
  else {
    $check = Variant::where('product_id', $product_id)->where('inventory', '>', 0)->count();
    $product->in_stock = $check ? 1 : 0;
  }
  $product->save();
}

function smartSearch(Request $request, Response $response) {
  $query = $request->getQueryParams();
  $products = Product::where('title', 'LIKE', '%'.$query['q'].'%')->where('display', 1)->skip(0)->take(5)->orderBy('updated_at', 'desc')->get();
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

function checkInventoryManagent($variant_id, $quantity) {
  $variant = Variant::find($variant_id);
  $product = Product::find($variant->product_id);
  if (!$product->inventory_management) return true;
  if ($variant->inventory >= $quantity) return true;
  return false;
}

function getSubRegion(Request $request, Response $response) {
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

function rotateImage(Request $request, Response $response) {
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

function initUser(Request $request, Response $response) {
  $check = User::where('role', 'super')->first();
  if($check) {
    return $response->withJson([
      'code' => -1,
      'message' => 'User exist'
    ]);
  }
  $user = new User;
  $user->name = 'Super';
  $user->email = 'admin@eyeteam.vn';
  $user->phone = '9999999999';
  $user->role = 'super';
  $user->password = password_hash('eyeteam.vn', PASSWORD_DEFAULT);
  $user->created_at = date('Y-m-d H:i:s');
  $user->updated_at = date('Y-m-d H:i:s');
  $user->save();
  return $response->withJson([
    'code' => 0,
    'message' => 'Create user success'
  ]);
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
