<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Collection.php");
require_once("../models/Product.php");
require_once("../models/helper.php");
require_once("../models/ProductColor.php");

class CollectionController extends Controller {

  public function show(Request $request, Response $response) {
    $handle = $request->getAttribute('link');
		if(substr($handle, -1) == '/') $handle = substr($handle, 0, -1);
		$collection = Collection::where('link', $handle)->first();
    if(!$collection) {
      $this->view->render($response, '404.pug');
      return $response->withStatus(404);
    }
    $collection_id = $collection->id;
		$main_handle = $collection->handle;
		$link = $_SERVER['REQUEST_URI'];
		$handles = explode('/', $link);
		$newBreadcrumb = array();
	  foreach($handles as $key => $handle) {
			if($handle && $main_handle != $handle && getTitleFromHandle($handle)) {
				$obj = new stdClass();
				$obj->title = getTitleFromHandle($handle);
				$obj->handle = '/' . $handle;
				array_push($newBreadcrumb, $obj);
			}
	  }
		$breadcrumb_collection = $newBreadcrumb;
		$breadcrumb_title = $collection->title;

		$page_number = 1;
		$params = $request->getQueryParams();
		if($params['page']) $page_number = $params['page'];
		$perpage = 20;
		$skip = ($page_number - 1) * $perpage;
    $query = Product::join('collection_product', 'collection_product.product_id', '=', 'product.id')
              ->where('collection_product.collection_id', $collection_id)
              ->where('product.display', 1)
              ->groupBy('product.id');

    $all_products = $query->select('product.*')->get();
		$total_pages = ceil(count($all_products) / $perpage);
    $products = $query->orderBy('product.in_stock', 'desc')->orderBy('product.updated_at', 'desc')->skip($skip)->take($perpage)->get();
		$products = Product::getInfoProduct($products);

		$list_brand = array();
    $list_material = array();
		$list_color = array();
    $list_size = array();
    $list_bag = array();

		$arr_temp_color = array();
    $arr_temp_special = array();

		foreach ($all_products as $key => $product) {
			if($product->brand && !in_array($product->brand, $list_brand)) array_push($list_brand, $product->brand);
      if($product->material && !in_array($product->material, $list_material)) array_push($list_material, $product->material);
      if($product->size && !in_array($product->size, $list_size)) array_push($list_size, $product->size);
      if($product->bag && !in_array($product->bag, $list_bag)) array_push($list_bag, $product->bag);
			$product_color = ProductColor::Join('color', 'product_color.color_id', '=', 'color.id')
				->where('product_color.product_id', $product->id)->select('color.name as name', 'color.hex as hex')->get();
			foreach ($product_color as $key => $color) {
				if(!in_array($color->name, $arr_temp_color)) {
					$obj = new stdClass();
					$obj->name = $color->name;
					$obj->style = 'background-color: ' . $color->hex;
					array_push($list_color, $obj);
					array_push($arr_temp_color, $color->name);
				}
			}
		}
    sort($list_brand);
    $title = $collection->title;
    if ($collection->meta_title) $title = $collection->meta_title;
    return $this->view->render($response, 'collection.pug', array(
      'title' => $title,
			'collection' => $collection,
      'list_product' => $products,
      'list_brand' => $list_brand,
      'list_material' => $list_material,
			'list_color' => $list_color,
      'list_size' => $list_size,
      'list_bag' => $list_bag,
			'total_pages' => $total_pages,
			'page_number' => $page_number,
			'breadcrumb_title' => $breadcrumb_title,
			'breadcrumb_collection' => $breadcrumb_collection,
		));
	}

  public function search(Request $request, Response $response) {
    $params = $request->getQueryParams();
    $search = $params['q'];
    if (!$search) return $response->withStatus(302)->withHeader('Location', '/');

    $breadcrumb_collection = array();
    $obj = new stdClass();
    $obj->handle = '#';
    $obj->title = 'Tìm kiếm';
    array_push($breadcrumb_collection, $obj);

		$page_number = 1;
		if($params['page']) $page_number = $params['page'];
		$perpage = 20;
		$skip = ($page_number - 1) * $perpage;
    $query = Product::where('product.title', 'LIKE', '%'.$search.'%')->where('product.display', 1);
    $all_products = $query->select('product.*')->get();
		$total_pages = ceil(count($all_products) / $perpage);
    $products = $query->orderBy('product.in_stock', 'desc')->orderBy('product.updated_at', 'desc')->skip($skip)->take($perpage)->get();
		$products = Product::getInfoProduct($products);

		$list_brand = array();
    $list_material = array();
		$list_color = array();
    $list_size = array();
    $list_bag = array();

		$arr_temp_color = array();
    $arr_temp_special = array();

		foreach ($all_products as $key => $product) {
			if($product->brand && !in_array($product->brand, $list_brand)) array_push($list_brand, $product->brand);
      if($product->material && !in_array($product->material, $list_material)) array_push($list_material, $product->material);
      if($product->size && !in_array($product->size, $list_size)) array_push($list_size, $product->size);
      if($product->bag && !in_array($product->bag, $list_bag)) array_push($list_bag, $product->bag);
			$product_color = ProductColor::Join('color', 'product_color.color_id', '=', 'color.id')
				->where('product_color.product_id', $product->id)->select('color.name as name', 'color.hex as hex')->get();
			foreach ($product_color as $key => $color) {
				if(!in_array($color->name, $arr_temp_color)) {
					$obj = new stdClass();
					$obj->name = $color->name;
					$obj->style = 'background-color: ' . $color->hex;
					array_push($list_color, $obj);
					array_push($arr_temp_color, $color->name);
				}
			}
		}
    sort($list_brand);
    return $this->view->render($response, 'collection.pug', array(
      'title' => 'Tìm kiếm',
      'list_product' => $products,
      'list_brand' => $list_brand,
      'list_material' => $list_material,
			'list_color' => $list_color,
      'list_size' => $list_size,
      'list_bag' => $list_bag,
			'total_pages' => $total_pages,
			'page_number' => $page_number,
			'breadcrumb_title' => $search,
			'breadcrumb_collection' => $breadcrumb_collection,
		));
	}

  public function brand(Request $request, Response $response) {
    $params = $request->getQueryParams();
    $brand = $request->getAttribute('name');
    if (!$brand) return $response->withStatus(302)->withHeader('Location', '/');
    $brand = Brand::where('handle', $brand)->first();
    if (!$brand) {
      $this->view->render($response, '404.pug');
      return $response->withStatus(404);
    }
    $breadcrumb_collection = array();
    $obj = new stdClass();
    $obj->handle = 'thuong-hieu';
    $obj->title = 'Danh sách thương hiệu';
    array_push($breadcrumb_collection, $obj);

		$page_number = 1;
		if($params['page']) $page_number = $params['page'];
		$perpage = 20;
		$skip = ($page_number - 1) * $perpage;
    $query = Product::where('product.brand', $brand->name)->where('product.display', 1)->where('product.price', '>', 0);
    $all_products = $query->select('product.*')->get();
		$total_pages = ceil(count($all_products) / $perpage);
    $products = $query->orderBy('product.in_stock', 'desc')->orderBy('product.updated_at', 'desc')->skip($skip)->take($perpage)->get();
		$products = Product::getInfoProduct($products);

    $list_material = array();
		$list_color = array();
    $list_special = array();
    $list_size = array();
    $list_bag = array();
    $list_brand = array($brand->name);

		$arr_temp_color = array();
    $arr_temp_special = array();

		foreach ($all_products as $key => $product) {
      if($product->material && !in_array($product->material, $list_material)) array_push($list_material, $product->material);
      if($product->size && !in_array($product->size, $list_size)) array_push($list_size, $product->size);
      if($product->bag && !in_array($product->bag, $list_bag)) array_push($list_bag, $product->bag);
			$product_color = ProductColor::Join('color', 'product_color.color_id', '=', 'color.id')
				->where('product_color.product_id', $product->id)->select('color.name as name', 'color.hex as hex')->get();
			foreach ($product_color as $key => $color) {
				if(!in_array($color->name, $arr_temp_color)) {
					$obj = new stdClass();
					$obj->name = $color->name;
					$obj->style = 'background-color: ' . $color->hex;
					array_push($list_color, $obj);
					array_push($arr_temp_color, $color->name);
				}
			}
      $product_special = ProductSpecial::Join('special', 'product_special.special_id', '=', 'special.id')
				->where('product_special.product_id', $product->id)->select('special.name as name')->get();
			foreach ($product_special as $key => $special) {
        if ($special->name && !in_array($special->name, $list_special)) array_push($list_special, $special->name);
			}
		}
    $title = $brand->name;
    if ($brand->meta_title) $title = $brand->meta_title;
    return $this->view->render($response, 'collection.pug', array(
      'title' => $title,
      'list_product' => $products,
      'list_material' => $list_material,
			'list_color' => $list_color,
      'list_brand' => $list_brand,
      'list_size' => $list_size,
      'list_bag' => $list_bag,
      'list_special' => $list_special,
			'total_pages' => $total_pages,
			'page_number' => $page_number,
			'breadcrumb_title' => $search,
			'breadcrumb_collection' => $breadcrumb_collection,
      'page_type' => 'brand',
      'brandName' => $brand->name
		));
	}

  public function discount50(Request $request, Response $response) {
		$page_number = 1;
		if($params['page']) $page_number = $params['page'];
		$perpage = 20;
		$skip = ($page_number - 1) * $perpage;
    $query = Product::where('product.discount', '>', 0)->where('product.display', 1)->where('product.price', '>', 0);
    $all_products = $query->select('product.*')->get();
		$total_pages = ceil(count($all_products) / $perpage);
    $products = $query->orderBy('product.in_stock', 'desc')->orderBy('product.updated_at', 'desc')->skip($skip)->take($perpage)->get();
		$products = Product::getInfoProduct($products);

    $list_brand = array();
    $list_material = array();
		$list_color = array();
    $list_special = array();
    $list_size = array();
    $list_bag = array();

		$arr_temp_color = array();
    $arr_temp_special = array();

		foreach ($all_products as $key => $product) {
      if($product->brand && !in_array($product->brand, $list_brand)) array_push($list_brand, $product->brand);
      if($product->material && !in_array($product->material, $list_material)) array_push($list_material, $product->material);
      if($product->size && !in_array($product->size, $list_size)) array_push($list_size, $product->size);
      if($product->bag && !in_array($product->bag, $list_bag)) array_push($list_bag, $product->bag);
			$product_color = ProductColor::Join('color', 'product_color.color_id', '=', 'color.id')
				->where('product_color.product_id', $product->id)->select('color.name as name', 'color.hex as hex')->get();
			foreach ($product_color as $key => $color) {
				if(!in_array($color->name, $arr_temp_color)) {
					$obj = new stdClass();
					$obj->name = $color->name;
					$obj->style = 'background-color: ' . $color->hex;
					array_push($list_color, $obj);
					array_push($arr_temp_color, $color->name);
				}
			}
      $product_special = ProductSpecial::Join('special', 'product_special.special_id', '=', 'special.id')
				->where('product_special.product_id', $product->id)->select('special.name as name')->get();
			foreach ($product_special as $key => $special) {
        if ($special->name && !in_array($special->name, $list_special)) array_push($list_special, $special->name);
			}
		}
    sort($list_brand);
    return $this->view->render($response, 'collection.pug', array(
      'list_product' => $products,
      'list_material' => $list_material,
			'list_color' => $list_color,
      'list_brand' => $list_brand,
      'list_size' => $list_size,
      'list_bag' => $list_bag,
      'list_special' => $list_special,
			'total_pages' => $total_pages,
			'page_number' => $page_number,
			'breadcrumb_title' => 'Giảm giá 50%',
		));
	}

  public function newProduct(Request $request, Response $response) {
		$page_number = 1;
		if($params['page']) $page_number = $params['page'];
		$perpage = 20;
		$skip = ($page_number - 1) * $perpage;
    $query = Product::where('product.display', 1)->where('product.price', '>', 0);
    $all_products = $query->select('product.*')->get();
		$total_pages = ceil(count($all_products) / $perpage);
    $products = $query->orderBy('product.in_stock', 'desc')->orderBy('product.updated_at', 'desc')->skip($skip)->take($perpage)->get();
		$products = Product::getInfoProduct($products);

    $list_brand = array();
    $list_material = array();
		$list_color = array();
    $list_special = array();
    $list_size = array();
    $list_bag = array();

		$arr_temp_color = array();
    $arr_temp_special = array();

		foreach ($all_products as $key => $product) {
      if($product->brand && !in_array($product->brand, $list_brand)) array_push($list_brand, $product->brand);
      if($product->material && !in_array($product->material, $list_material)) array_push($list_material, $product->material);
      if($product->size && !in_array($product->size, $list_size)) array_push($list_size, $product->size);
      if($product->bag && !in_array($product->bag, $list_bag)) array_push($list_bag, $product->bag);
			$product_color = ProductColor::Join('color', 'product_color.color_id', '=', 'color.id')
				->where('product_color.product_id', $product->id)->select('color.name as name', 'color.hex as hex')->get();
			foreach ($product_color as $key => $color) {
				if(!in_array($color->name, $arr_temp_color)) {
					$obj = new stdClass();
					$obj->name = $color->name;
					$obj->style = 'background-color: ' . $color->hex;
					array_push($list_color, $obj);
					array_push($arr_temp_color, $color->name);
				}
			}
      $product_special = ProductSpecial::Join('special', 'product_special.special_id', '=', 'special.id')
				->where('product_special.product_id', $product->id)->select('special.name as name')->get();
			foreach ($product_special as $key => $special) {
        if ($special->name && !in_array($special->name, $list_special)) array_push($list_special, $special->name);
			}
		}
    sort($list_brand);
    return $this->view->render($response, 'collection.pug', array(
      'list_product' => $products,
      'list_material' => $list_material,
			'list_color' => $list_color,
      'list_brand' => $list_brand,
      'list_size' => $list_size,
      'list_bag' => $list_bag,
      'list_special' => $list_special,
			'total_pages' => $total_pages,
			'page_number' => $page_number,
			'breadcrumb_title' => 'Hàng mới về',
		));
	}

  public function filter (Request $request, Response $response) {
    $body = $request->getParsedBody();
		$collection_id = $body['collection_id'];
		$search = $body['search'];
		$url = $body['url'];
		$page_number = 1;
		$perpage = 20;
		if($body['page']) $page_number = $body['page'];
		$skip = ($page_number - 1) * $perpage;
    $query =  Product::where('product.display', 1)->where('product.price', '>', 0)->where('in_stock', 1);

		if ($collection_id) $query = $query->join('collection_product', 'product.id', '=', 'collection_product.product_id')->where('collection_product.collection_id', $collection_id);
		else if ($search) {
			$query = $query->where('product.title', 'LIKE', '%'.$search.'%');
		}
		$check_sort = false;
		if($url) {
			$arr_item = explode('&', $url);
			foreach ($arr_item as $key => $item) {
				if(strpos($item, 'size') !== false) {
					$arr_size = str_replace('size=', '', $item);
					$arr_size = explode('+', $arr_size);
					$GLOBALS['arr_size'] = $arr_size;
					$query = $query->where(function($q) {
						global $arr_size;
						foreach ($arr_size as $key => $size) {
							$q = $q->orWhere('product.size', $size);
						}
					});
				} else if(strpos($item, 'brand') !== false) {
					$arr_brand = str_replace('brand=', '', $item);
					$arr_brand = explode('+', $arr_brand);
					$GLOBALS['arr_brand'] = $arr_brand;
					$query = $query->where(function($q) {
						global $arr_brand;
						foreach ($arr_brand as $key => $brand) {
							$q = $q->orWhere('product.brand', 'LIKE', '%'.$brand.'%');
						}
					});
        } else if(strpos($item, 'bag') !== false) {
					$arr_bag = str_replace('bag=', '', $item);
					$arr_bag = explode('+', $arr_bag);
					$GLOBALS['arr_bag'] = $arr_bag;
					$query = $query->where(function($q) {
						global $arr_bag;
						foreach ($arr_bag as $key => $bag) {
							$q = $q->orWhere('product.bag', 'LIKE', '%'.$bag.'%');
						}
					});
        } else if(strpos($item, 'material') !== false) {
					$arr_material = str_replace('material=', '', $item);
					$arr_material = explode('+', $arr_material);
					$GLOBALS['arr_material'] = $arr_material;
					$query = $query->where(function($q) {
						global $arr_material;
						foreach ($arr_material as $key => $material) {
							$q = $q->orWhere('product.material', 'LIKE', '%'.$material.'%');
						}
					});
				} else if(strpos($item, 'price=') !== false) {
					$price = str_replace('price=', '', $item);
          $price = str_replace('(', '', $price);
          if (strpos($price, '-') !== false) {
            $price = explode('-', $price);
            $query = $query->whereBetween('product.price', [$price[0], $price[1]]);
          } else if (strpos($price, '<')) {
            $price = substr($price, 1);
            $query = $query->where('product.price', '<', $price);
          } else if (strpos($price, '>')) {
            $price = substr($price, 1);
            $query = $query->where('product.price', '>', $price);
          }
				} else if(strpos($item, 'sort') !== false) {
					$arr_sort = str_replace('sort=', '', $item);
					$arr_sort = explode(':', $arr_sort);
					$query = $query->orderBy($arr_sort[0], $arr_sort[1]);
					$check_sort = true;
				}
			}
			if(!$check_sort) $query->orderBy('product.in_stock', 'desc')->orderBy('product.updated_at', 'desc');
		} else $query =  $query->orderBy('product.in_stock', 'desc')->orderBy('product.updated_at', 'desc');

    $all = $query->distinct()->get();
    $products = $query->groupBy('product.id')->skip($skip)->take($perpage)->get();
		$total_pages = ceil(count($all) / (int)$perpage);
		if(count($products)) {
      $products = Product::getInfoProduct($products);
			return $this->view->render($response, 'collection-filter.pug', [
				'products' => $products,
        'page_number' => $page_number,
        'total_pages' => $total_pages
			]);
		}
		return 'empty';
	}


  public function listAllBrand(Request $request, Response $response) {
    $this->view->render($response, 'brand.pug', array(
			'title' => 'Thương hiệu',
			'breadcrumb_title' => 'Thương hiệu'
		));
		return $response;
  }
}

?>
