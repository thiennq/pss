<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Product.php");
require_once("../models/Collection.php");
require_once("../models/Meta.php");
require_once("../models/CollectionProduct.php");
require_once("../models/Inventory.php");
require_once("../models/Image.php");
require_once("../models/ProductColor.php");
require_once("../models/ProductSpecial.php");

use Illuminate\Database\Connection as DB;
PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);

class AdminProductController extends AdminController {

  public function index(Request $request, Response $response) {
    $products = Product::join('inventory', 'product.id', '=', 'inventory.product_id')
    ->select(
      'product.featured_image as featured_image',
      'product.barcode as barcode',
      'product.title as title',
      'product.id as id',
      'product.display as display',
      DB::raw('SUM(inventory.inventory) as inventory'))->groupBy('product.id')->get();
		return $this->view->render($response, 'admin/product_list.pug', [
			'products' => $products
		]);
	}

	public function create(Request $request, Response $response) {
		$list_collection = Collection::orderBy('breadcrumb', 'asc')->get();
		foreach ($list_collection as $key => $collection) {
			$collection['breadcrumb'] = str_replace(',', '/', $collection['breadcrumb']);
			$collection['breadcrumb'] = strtoupper($collection['breadcrumb']);
		}
		$brand = Brand::all();
		$color = Color::all();
		return $this->view->render($response, 'admin/product_new.pug', array(
			'list_collection' => $list_collection,
			'brand' => $brand,
			'color' => $color
		));
	}

	public function show(Request $request, Response $response) {
		$id = $request->getAttribute('id');
    $data = Product::find($id);
		if(!$data) return $this->view->render($response, '404.pug');
		$list_collection = Collection::orderBy('title', 'asc')->get();
		$brand = Brand::orderBy('name', 'asc')->get();
		$color = Color::orderBy('name', 'asc')->get();
    $special = Special::orderBy('name', 'asc')->get();
		$material = Material::orderBy('name', 'asc')->get();
    $size = Size::orderBy('name', 'asc')->get();
    $bag = Bag::orderBy('name', 'asc')->get();
		$inventory = Inventory::join('branch', 'inventory.branch_id', '=', 'branch.id')->where('inventory.product_id', $id)->get();
		$data->inventory = $inventory;
		$data->check_inventory = count($inventory);
		$data->total_inventory = Inventory::where('product_id', $id)->sum('inventory');
    $data->list_image = Image::getImage('product', $id);
		$arr_collection_id = CollectionProduct::where('product_id', $id)->get();
		$data['collection_id'] = $arr_collection_id;
		$arr_color_id = ProductColor::where('product_id', $id)->get();
		$data['color_id'] = $arr_color_id;
    $arr_special_id = ProductSpecial::where('product_id', $id)->get();
		$data['special_id'] = $arr_special_id;
		$table_specification = Meta::where('key', 'table_specification')->first();
		$table_specification = $table_specification->value;

		return $this->view->render($response, 'admin/product_edit.pug', array(
			'data' => $data,
			'brand' => $brand,
      'size' => $size,
			'color' => $color,
      'bag' => $bag,
      'special' => $special,
			'table_specification' => $table_specification,
			'material' => $material,
			'list_collection' => $list_collection
		));
	}

	public function update(Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$body = $request->getParsedBody();
		$Product = Product::find($id);
		if($Product) {
      $Product->group_id = $body['group_id'];
			$Product->title = $body['title'];
      $Product->handle = $body['handle'];
      if($body['barcode']) $Product->barcode = $body['barcode'];
			// $Product->price = $body['price'];
			// $Product->price_compare = $body['price_compare'];
			$Product->description = $body['description'];
			// $Product->meta_description = $body['meta_description'];
			$Product->material = $body['material'];
      $Product->size = $body['size'];
      $Product->bag = $body['bag'];
			$Product->specification = $body['specification'];
      $Product->content = $body['content'];
      $list_image = $body['list_image'];
      if(count($list_image)) {
        $list_image = renameListImage($list_image, time());
        $list_image = renameListImage($list_image, $body['handle']);
        $Product->featured_image = $list_image[0];
      }
			$Product->brand = $body['brand'];
			$Product->meta_robots = $body['meta_robots'];
      $Product->display = $body['display'];
      $Product->dropship = $body['dropship'];
			$Product->updated_at = $body['updated_at'] ? $body['updated_at'] : date('Y-m-d H:i:s');
			if($Product->save()) {
        Image::where('typeId', $id)->where('type', 'product')->delete();
        if(count($list_image)) {
  				foreach ($list_image as $key => $value) {
            error_log($value);
  					Image::store($value, 'product', $id);
  				}
        }
				$collection_id = $body['collection_id'];
				CollectionProduct::where('product_id', $id)->delete();
				foreach ($collection_id as $key => $value) {
          $parent = Collection::find($value)->parent_id;
          if($parent) CollectionProduct::store($parent, $id);
					CollectionProduct::store($value, $id);
				}

				$color_id = $body['color_id'];
				ProductColor::where('product_id', $id)->delete();
				foreach ($color_id as $key => $value) {
					ProductColor::store($id, $value);
				}
        $special_id = $body['special_id'];
				ProductSpecial::where('product_id', $id)->delete();
				foreach ($special_id as $key => $value) {
					ProductSpecial::store($id, $value);
				}
        setMemcached("product_index", '');
        setMemcached("product_". $Product->handle, '');
				return $response->withJson(array(
					'code' => 0,
					'message' => 'Updated'
				));
			}
			return $response->withJson(array(
				'code' => -1,
				'message' => 'Error'
			));
		}
		return $response->withJson(array(
			'code' => -1,
			'message' => 'Unkown Product'
		));
	}

	public function delete(Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$Product = Product::find($id);
		if($Product) {
			removeImage($Product->featured_image);
			$list_image = $Product->list_image;
			$arr = explode(',', $list_image);
			foreach ($arr as $key => $image) {
				if($image) removeImage($image);
			}
			if($Product->delete()) {
				return $response->withJson(array(
					'code' => 0,
					'message' => 'Deleted'
				));
			}
			return json_encode(array(
				'code' => -1,
				'message' => 'Error'
			));
		}
		return json_encode(array(
			'code' => -1,
			'message' => 'Unknown Product'
		));
	}

	public function specification(Request $resquest, Response $response) {
    $data = getMeta('table_specification');
		return $this->view->render($response, 'admin/specification.pug', [
			'data' => $data
		]);
	}

	public function deleteImage(Request $request, Response $response) {
		$id = $request->getAttribute('id');
		$image = Image::find($id);
		if($image) {
			$image->delete();
      removeImage($image->name);
			return $response->withJson(array(
				'code' => 0,
				'message' => 'Deleted',
			));
		}
		return $response->withJson(array(
			'code' => -1,
			'message' => 'Error',
		));
	}

  public function renderImageTinymce(Request $request, Response $response) {
    $dir = ROOT . '/public/uploads/images';
    $images = scandir($dir);
    array_shift($images);
    array_shift($images);
    return $this->view->render($response, 'admin/tinymce-upload.pug', array(
      "title" => "Upload image",
      "images" => $images,
      "total" => count($images)
		));
  }
}

?>
