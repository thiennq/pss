<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Product.php");
require_once("../models/Meta.php");
require_once("../models/Inventory.php");
require_once("../models/Branch.php");
require_once("../models/Region.php");
require_once("../models/SubRegion.php");
require_once("../models/Article.php");

use GuzzleHttp\Client;

class CrawlerController extends Controller {

  public function updateInventory(Request $request, Response $response) {
    $meta_page = Meta::where('key', 'last_page_crawler_inventory')->first();
    $last_page = $meta_page->value;
    $page = $_GET['page'] ? $_GET['page'] : $last_page;
    $perpage = $_GET['perpage'] ? $_GET['perpage'] : 2;
    $skip = ($page - 1) * $perpage;
    $client = new \GuzzleHttp\Client();
    $branchs = Branch::Skip($skip)->take($perpage)->orderBy('id', 'asc')->get();
    $page_new = $last_page + 1;
    if(count($branchs) < $perpage) {
      $page_new = 1;
      $branchs = Branch::take($perpage)->orderBy('id', 'asc')->get();
    }
    $products = Product::all();
    foreach ($branchs as $key => $branch) {
      $this->updateInventoryOneBranch($branch->id);
    }
    $meta_page->value = $page_new;
    $meta_page->updated_at = date('Y-m-d H:i:s');
    $meta_page->save();
  }

  public function updateInventoryOneBranch($branch_id=null) {
    $branch_id = $_GET['branch_id'] ? $_GET['branch_id'] : $branch_id;
    if(!$branch_id) return false;
    $client = new \GuzzleHttp\Client();
    $products = Product::all();
    $this->addQtyProductByBranch($branch_id, $products);
    $inventory = Inventory::where('branch_id', $branch_id)->orderBy('product_id', 'asc')->select('product_id', 'inventory')->get();
    $db = [];
    foreach ($inventory as $key => $item) {
      $db[$item->product_id] = $item->inventory;
    }
    foreach ($products as $key => $p) {
      if(!$db[$p->id]) $db[$p->id] = -999;
    }
    $url = 'http://erp.tga.com.vn:9002/api/inventory/branch/' . $branch_id . '?perpage=1000000000000';
    echo "<br/>";
    echo "<br/>";
    echo "<br/>";
    echo "Url: " . $url;
    echo "<br/>";
    // echo "DB: " . json_encode($db);
    // echo "<br/>";
    $result = $client->request('GET', $url);
    $json = $result->getBody();
    $json = json_decode($json, true);
    if(!$json['code']) {
      $api = $json['data'];
      echo "Count data API: " . count($api);
      echo "<br/>";
      if(count($api)) {
        echo "Data API";
        echo "<br/>";
        echo json_encode($api);
        echo "<br/>";
        $updates = [];
        foreach ($db as $index => $qty) {
          if($db[$index] != $api[$index]) {
            $new_qty = $api[$index] ? $api[$index] : 0;
            $updates[$index] = $new_qty;
          }
        }
        echo "Update: " . json_encode($updates);
        echo "<br/>";
        foreach ($updates as $index => $qty) {
          Inventory::where('branch_id', $branch_id)->where('product_id', $index)->update(['inventory' => $qty]);
          $check = Inventory::join('branch', 'branch.id', '=', 'inventory.branch_id')->where('branch.calc_inventory', 1)->where('inventory.product_id', $index)->where('inventory.inventory', '>', 0)->count();
          $in_stock = 1;
          if(!$check) $in_stock = 0;
          Product::where('id', $index)->update(['in_stock' => $in_stock]);
          // setMemcached("product_". $Product->handle, '');
        }
      }
    }
  }

  public function addQtyProductByBranch($branchId, $products) {
    foreach ($products as $key => $product) {
      $inventory = Inventory::where('branch_id', $branchId)->where('product_id', $product->id)->first();
      if(!$inventory) {
        $inventory = new Inventory;
        $inventory->branch_id = $branchId;
        $inventory->product_id = $product->id;
        $inventory->inventory = 0;
        $inventory->created_at = date('Y-m-d H:i:s');
        $inventory->updated_at = date('Y-m-d H:i:s');
        $inventory->save();
      }
    }
  }

  public function getAllProduct(Request $request, Response $response) {
		$client = new \GuzzleHttp\Client();
    $url = 'http://erp.tga.com.vn:9002/api/product';
    $time = Meta::where('key', 'last_time_crawler_product')->first();
    $last_time = $time->value;
    $page = $_GET['page'] ? $_GET['page'] : 1;
    $perpage = $_GET['perpage'] ? $_GET['perpage'] : 1000;
    $from = $_GET['from'] ? $_GET['from'] : $last_time;
    $url .= "?page=" . $page . "&perpage=" . $perpage .'&from=' . $from;
		$result = $client->request('GET', $url);
		$json = $result->getBody();
    $json = json_decode($json, true);
    $list_product = [];
    try {
      if(!$json['code']) {
        $products = $json['data'];
        foreach ($products as $key => $product) {
          $obj = new stdClass();
          $obj->id = $product['id'];
          $obj->group_id = $product['groupID'];
          $obj->title = $product['name'];
          $handle = convertHandle($obj->title);
          while(1) {
            $check_handle = checkHandle($handle);
            if(!$check_handle) $handle = $handle . '-1';
            else {
              $handle = $check_handle;
              break;
            }
          }
          $obj->handle = $handle;
          $obj->description = $product['description'];
          $obj->price = $product['list_price'];
          $obj->price_compare = $product['listed_price'];
          if((int)$obj->price_compare > (int)$obj->price) $obj->discount = (int) $obj->price_compare > (int) $obj->price;
          $obj->barcode = $product['barcode'];
          if($product['trademarkAttr'])  $obj->brand = $product['trademarkAttr'][1];
          Product::store($obj);
          if($product['trademarkAttr']) {
            $brand = new stdClass();
            $brand->id = $product['trademarkAttr'][0];
            $brand->name = $product['trademarkAttr'][1];
            $brand->handle = convertHandle($brand->name);
            Brand::store($brand);
          }
        }
        $time->value = strtotime(date('Y-m-d')) * 1000;
        $time->updated_at = date('Y-m-d');
        $time->save();
      }
    } catch (Exception $e) {
      return $response->withJson([
        'code' => -1,
        'message' => 'Caught exception: ',  $e->getMessage()
      ]);
		}
    return $response->withJson([
			'code' => 0,
			'message' => 'Success'
		]);
	}

  public function getAllBranch() {
		$client = new \GuzzleHttp\Client();
    $url = 'http://erp.tga.com.vn:9002/api/branch';
		$result = $client->request('GET', $url);
		$json = $result->getBody();
    $json = json_decode($json, true);
    try {
      if(!$json['code']) {
        $branchs = $json['data'];
        echo json_encode($branchs);
        foreach ($branchs as $key => $branch) {
          $obj = new stdClass();
          $obj->id = $branch['id'];
          $obj->name = $branch['display_name'];
          if(Branch::store($obj)) {
            echo $obj->name;
            echo "<br/>";
          }
        }
      }
    } catch (Exception $e) {
				echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	}

  public function updateOrderState(Request $request, Response $response) {
    $client = new \GuzzleHttp\Client();
    $url = 'http://erp.tga.com.vn:9002/api/order';
    $time = Meta::where('key', 'last_time_crawler_order')->first();
    $last_time = $time->value;
    $page = $_GET['page'] ? $_GET['page'] : '1';
    $perpage = $_GET['perpage'] ? $_GET['perpage'] : 100;
    $from = $_GET['from'] ? $_GET['from'] : $last_time;
    $url .= "?page=" . $page . "&perpage=" . $perpage .'&from=' . $from;
		$result = $client->request('GET', $url);
		$json = $result->getBody();
    $json = json_decode($json, true);
    try {
      if(!$json['code']) {
        $orders = $json['data'];
        $flag = true;
        foreach ($orders as $key => $item) {
          $order_id = $item['id'];
          $state = $item['state'];
          $order = Order::where('id_odoo', $order_id)->first();
          if($order) {
            if ($state == 'draft') $state = 'new';
            $order->order_status = $state;
            $order->updated_at = date('Y-m-d H:i:s');
            if (!$order->save()) $flag = false;
          }
        }
        if ($flag) {
          $milliseconds = round(microtime(true) * 1000);
          $time->value = $milliseconds;
          $time->updated_at = date('Y-m-d H:i:s');
          $time->save();
        }
      }
    } catch (Exception $e) {
        return $response->withJson([
    			'code' => -1,
    			'message' => 'Caught exception: ',  $e->getMessage()
    		]);
		}
    return $response->withJson([
			'code' => 0,
			'message' => 'Success'
		]);
	}

  public function updateOneOrderState(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $order = Order::where('id', $id)->where('id_odoo', '!=', '')->first();
    if (!$order) {
      return $response->withJson([
        'code' => -1,
        'message' => 'Not found'
      ]);
    }
    $client = new \GuzzleHttp\Client();
    $url = 'http://erp.tga.com.vn:9002/api/order/' . $order->id_odoo;
		$result = $client->request('GET', $url);
		$json = $result->getBody();
    $json = json_decode($json, true);
    if(!$json['code']) {
      $data = $json['data'];
      $order->order_status = $data['state'];
      if ($data['state'] == 'draft') $order->order_status = 'new';
      $order->updated_at = date('Y-m-d H:i:s');
      if ($order->save()) {
        return $response->withJson([
    			'code' => 0,
    			'message' => 'Success'
    		]);
      }
      return $response->withJson([
  			'code' => -1,
  			'message' => 'Caught exception: ',  $e->getMessage()
  		]);
    }
	}

  public function getListCity(Request $request, Response $response) {
		$client = new \GuzzleHttp\Client();
    $url = 'http://erp.tga.com.vn:9002/api/city?limit=1000';
		$result = $client->request('GET', $url);
		$json = $result->getBody();
    $json = json_decode($json, true);
    try {
      if(!$json['code']) {
        $region = $json['data'];
        foreach($region as $key => $item) {
          $obj = new stdClass();
          $obj->id = $item['id'];
          $obj->name = $item['name'];
          Region::store($obj);
        }
      }
    } catch (Exception $e) {
				echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	}

  public function getListDistrict(Request $request, Response $response) {
		$client = new \GuzzleHttp\Client();
    $url = 'http://erp.tga.com.vn:9002/api/district?limit=1000';
		$result = $client->request('GET', $url);
		$json = $result->getBody();
    $json = json_decode($json, true);
    try {
      if(!$json['code']) {
        $region = $json['data'];
        foreach($region as $key => $item) {
          $obj = new stdClass();
          $obj->id = $item['id'];
          $obj->name = $item['name'];
          $obj->region_id = $item['state_id'][0];
          SubRegion::store($obj);
        }
      }
    } catch (Exception $e) {
				echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	}

	public function createMeta() {
    $key = $_GET['key'];
    $value = $_GET['value'];
    Meta::store($key, $value);
    echo 'Created';
	}

  public function createOrderOdoo() {
    $orders = Order::where('id_odoo', '')->get();
    foreach ($orders as $key => $order) {
      if($order->count_create_odoo && $order->count_create_odoo < 6) {
        $order_odoo = Order::getInfoOdoo($order->id);
        $odoo_id = Order::createOrderOdoo($order_odoo);
        if($odoo_id) Order::where('id', $order->id)->update(['id_odoo' => $odoo_id]);
        else {
          $temp = Order::find($order->id);
          $temp->count_create_odoo = $temp->count_create_odoo + 1;
          $temp->save();
          LogOrderOdoo::store($order->id, $_SESSION['error_create_odoo']);
          Order::postTelegram($order->id, $_SESSION['error_create_odoo']);
        }
      }
    }
  }
}

?>
