<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class TestController extends Controller {

  public function test() {
    $page = $_GET['page'] ? $_GET['page'] : 1;
    $perpage = $_GET['perpage'] ? $_GET['perpage'] : 100;
    $skip = ((int) $page - 1) * $perpage;
    $product = Product::Skip($skip)->take($perpage)->get();
    foreach ($product as $key => $value) {
      echo $value->handle . '</br>';
      $product = Product::find($value->id);
      $image = Image::where('type', 'product')->where('typeId', $value->id)->first();
      $product->featured_image = '';
      if ($image) {
        $product->featured_image = $image->name;
      }
      $product->save();
    }
  }

  public function redirect() {
		$path = ROOT . '/public/excel/Redirect.xlsx';
    $inputFileType = PHPExcel_IOFactory::identify($path);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($path);
    $objWorksheet = $objPHPExcel->getSheet(0);
    $highestRow = $objWorksheet->getHighestRow();
    for ($row = 2; $row <= $highestRow; $row++) {
      $old = $objWorksheet->getCellByColumnAndRow(0, $row)->getCalculatedValue();
      $new = $objWorksheet->getCellByColumnAndRow(1, $row)->getCalculatedValue();
      $redirect = Redirect::where('old', $old)->first();
      if(!$redirect) {
        echo $old . ' --- ' . $new  . '<br/>';
        $check = Redirect::where('old', $old)->where('new', $new)->first();
        if(!$check) {
          $redirect = new Redirect;
          $redirect->old = $old;
          $redirect->new = $new;
          $redirect->created_at = date('Y-m-d H:i:s');
          $redirect->updated_at = date('Y-m-d H:i:s');
          if($redirect->save()) error_log($old . '--' . $new);
        }
      }
    }
  }


  public function checkInStock() {
    $page = $_GET['page'];
    $perpage = $_GET['perpage'];
    $skip = ($page -  1) * $perpage;
    $products = Product::Skip($skip)->take($perpage)->get();
    $count = 0;
    foreach ($products as $key => $product) {
      $check = Inventory::join('branch', 'branch.id', '=', 'inventory.branch_id')->where('branch.calc_inventory', 1)->where('inventory.product_id', $product->id)->where('inventory.inventory', '>', 0)->count();
      $in_stock = 1;
      if(!$check) $in_stock = 0;
      Product::where('id', $product->id)->update(['in_stock' => $in_stock]);
      echo $check . ' ----- ' .$product->title;
      echo "<br/>";
      $count++;
    }
    echo "Total: " . $count;
    echo "<br/>";
  }

  public function replaceLinkArticle() {
    $articles = Article::where('type', 'tin-tuc')->get();
    $countAll = 0;
    $countReplace = 0;
    $countNotfound = 0;
    foreach ($articles as $key => $article) {
      $article_id = $article->id;
      $article_content = $article->content;
      // echo $article_content . '<br/>';
      $dom = new DOMDocument;
      $dom->loadHTML($article->content);
      $links = $dom->getElementsByTagName('a');
      foreach ($links as $link){
        $href = $link->getAttribute('href');
        // echo $href .'<br/>';
        if($href && $href != 'http://mia.vn/' && $href != 'http://mia.vn' && strlen($href) > 5) {
          $countAll++;
          $redirect = Redirect::where('old', $href)->first();
          $check = true;
          if($redirect) $new_href = $redirect->new;
          else if(strpos($href, 'http://mia.vn/products') !== false) $new_href = str_replace('/products/', '/san-pham/', $href);
          else if(strpos($href, '../../collections') !== false) {
            $new_href = str_replace('../../collections', 'http://mia.vn', $href);
            $params = explode('/', $new_href);
            $brand = array_pop($params);
            if(Brand::where('handle', $brand)->first()) $new_href = str_replace('../../collections', 'http://mia.vn/thuong-hieu', $href);
          }
          else if(strpos($href, '../../') !== false) $new_href = str_replace('../../', 'http://mia.vn', $href);
          else $check = false;
          if($check) {
            // echo $new_href  . '<br/>';
            $article_content = str_replace($href, $new_href, $article_content);
            $temp = Article::find($article_id);
            $temp->content = $article_content;
            $temp->save();
            $countReplace++;
          } else {
            echo $href .'<br/>';
            $countNotfound++;
          }
        }
      }
    }
    echo "countAll: " . $countAll;
    echo "<br/>";
    echo "countReplace: " . $countReplace;
    echo "<br/>";
    echo "countNotfound: " . $countNotfound;
  }

  public function url_exists($url) {
    $file_headers = @get_headers($url);
    if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') return 0;
    return 1;
  }

  public function renameHandle() {
    $products = Product::all();
    $count = 0;
    foreach ($products as $key => $product) {
      echo $product->title . '--->' . $product->handle;
      echo "<br/>";
      if($product->handle != convertHandle($product->title)) {
        echo $product->title . '--->' . $product->handle;
        echo "<br/>";
        $count++;
        Product::where('id', $product->id)->update(['handle' => convertHandle($product->title)]);
      }
    }
    echo "Total: " . $count;
  }

  public function images() {
    $products = Product::where('display', 1)->get();
    foreach ($products as $key => $product) {
      $id = $product->id;
      // $image = Image::where('product_id', $id)->first();
      // if($product->featured_image == $image->name) {
      //   echo $id . ' ---- ' .$image->name;
      //   echo "<br/>";
      // }
      // if($image->name) {
      //   Product::where('id', $id)->update(['featured_image' => $image->name]);
      //   echo $id . ' ---- ' .$image->name;
      //   echo "<br/>";
      // }
    }
  }
}
?>
