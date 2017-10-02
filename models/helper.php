<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once('Product.php');
require_once('Collection.php');

$GLOBALS['size'] = [240, 480, 640, 1024, 2048];


function setMemcached($key, $value, $time=30*24*60*60) {
  global $memcached;
  $memcached->set($key, $value, $time);
}

function getMemcached($key) {
  global $memcached;
  if(strpos(HOST, 'localhost') !== false && $memcached) return $memcached->get($key);
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

function handle($str) {
  $str = trim($str);
  $str = strtolower($str);
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
  $str = preg_replace('/[^A-Za-z0-9-]+/', '-', $str);
  $str = str_replace(' ', '-', $str);
  $str = str_replace('.', '-', $str);
  $str = str_replace('--', '-', $str);
  $str = str_replace('--', '-', $str);
  $str = str_replace('--', '-', $str);
  if(substr($str, -1) == '-') $str = substr($str, 0, -1);
  return $str;
}

function convertHandle($str) {
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

function checkHandle($handle) {
  $product = Product::where('handle', $handle)->count();
  $collection = Collection::where('link', $handle)->count();
  if($product || $collection) return 0;
  return $handle;
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
