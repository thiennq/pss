<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Connection as DB;

class Product extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'product';

    public function store($data) {
      $product = Product::where('title', $data['title'])->first();
      if ($product) return -1;
      $product = new Product;
      $product->title = $data['title'];
      $product->handle = createHandle($data['title']);
      $product->featured_image = '';
      $product->description = $data['description'];
      $product->in_stock = 0;
      $product->display = (int) $data['display'] ? 1 : 0;
      $product->inventory_management = (int) $data['inventory_management'] ? 1 : 0;
      $product->view = 0;
      $product->sell = 0;
      $product->meta_title = $data['meta_title'];
      $product->meta_description = $data['meta_description'];
      $product->meta_robots = $data['meta_robots'];
      $product->created_at = date('Y-m-d H:i:s');
      $product->updated_at = date('Y-m-d H:i:s');
      if ($product->save()) return $product->id;
      return -3;
    }

    public function getInfoProduct($products) {
      foreach ($products as $key => $value) {
  			$product_id = $value['id'];
        $firstVariant = Variant::where('product_id', $value['id'])->first();
  			$value['display_discount'] = false;
        $value['percent'] = 0;
        $value['price'] = $firstVariant['price'];
        $value['price_compare'] = $firstVariant['price_compare'];
        if($firstVariant['price_compare'] && $firstVariant['price_compare'] > $firstVariant['price']) {
          $value['discount'] = $firstVariant['price_compare'] - $firstVariant['price'];
          $value['percent'] = ($value['discount'] / $firstVariant['price_compare']) * 100;
          $value['percent'] = round($value['percent'], 0) .'%';
          $value['display_discount'] = true;
  			}
  		}
      return $products;
    }

    public function getRelatedProducts($id) {
      return Product::Join('collection_product', 'collection_product.product_id', '=', 'product.id')
      ->where('collection_product.collection_id', $collection_id_related)
      ->where('product.display', 1)->where('product.id', '!=', $product->id)->select('product.*')->where('product.in_stock', 1)->orderBy('product.updated_at', 'desc')->take(6)->get();
    }

    public function checkFilter($obj) {
      $page_type = $obj->page_type;
  		$brand = $obj->brand;
  		$color = $obj->color;
  		$price = $obj->price;
      $collection_id = $obj->collection_id;
      $query = Product::where('product.display', 1)->where('product.price', '>', 0);
  		if($page_type == 'collection') {
  			$query = $query->join('collection_product', 'collection_product.product_id', '=', 'product.id')
  						->where('collection_product.collection_id', $collection_id);
  		} else if($page_type == 'discount') $query = $query->where('discount', '>', 0);
      else if($page_type == 'brand') $query = $query->where('product.brand', $obj->brand_name);
      else if($page_type == 'search') $query = $query->where('title', 'LIKE','%'.$obj->keyword.'%');
      else if($page_type == 'tag') $query = $query->join('collection_product', 'collection_product.product_id', '=', 'product.id')->join('collection_tag', 'collection_tag.collection_id', '=', 'collection_product.collection_id')->where('collection_tag.handle', $obj->tag_handle);

  		if(strlen($brand)) {
  			$arr_brand = explode('+', $brand);
  			$GLOBALS['arr_brand'] = $arr_brand;
  			$query = $query->where(function ($q) {
  				global $arr_brand;
  				foreach ($arr_brand as $key => $brand) {
            if(strpos($brand, '%20')) $brand = str_replace('%20', ' ', $brand);
  					$q = $q->orWhere('product.brand', $brand);
  				}
  			});
  		}
  		if(strlen($color)) {
  			$arr_color = explode(' ', $color);
  			$GLOBALS['arr_color'] = $arr_color;
        $query = $query->join('product_color', 'product.id', '=', 'product_color.product_id');
  			$query = $query->where(function ($q) {
  				global $arr_color;
          $arr_color_id = array();
          foreach ($arr_color as $key => $color) {
            $color_id = Color::where('name', $color)->first()->id;
            array_push($arr_color_id, $color_id);
  				}
          foreach ($arr_color_id as $key => $color_id) {
            $q = $q->orWhere('product_color.color_id', $color_id);
          }
  			});
  		}
  		if(strlen($price)) {
  			$price = str_replace('(', '', $price);
  			$price = str_replace(')', '', $price);
  			$arr_price = explode('||', $price);
  			$GLOBALS['arr_price'] = $arr_price;
  			$query = $query->where(function ($q) {
  				global $arr_price;
  				foreach ($arr_price as $key => $item) {
  					if(strpos($item, '<') !== false) {
  						$price = str_replace('<', '', $item);
  						$q = $q->orWhere('product.price', '<', $price);
  					} else if(strpos($item, '>') !== false) {
  						$price = str_replace('>', '', $item);
  						$q = $q->orWhere('product.price', '>', $price);
  					} else if(strpos($item, '-') !== false) {
  						$arr_temp = explode('-', $item);
  						$GLOBALS['arr_temp'] = $arr_temp;
  						$q = $q->orWhere(function($k) {
  							global $arr_temp;
  							$k = $k->where('price', '>', $arr_temp[0]);
  							$k = $k->where('price', '<', $arr_temp[1]);
  						});
  					}
  				}
  			});
  		}
      $query = $query->distinct()->select('product.*');
      return $query;
    }

    public function updateSell($id, $quantity) {
      $product = Product::find($id);
      if($product) {
        $sell = (int) $quantity;
        if($product->sell) $sell = $product->sell + $sell;
        $product->sell = $sell;
        $product->save();
      }
    }

    public function updateView($id) {
      $product = Product::find($id);
      if($product) {
        $view = 1;
        if($product->view) $view = $product->view + 1;
        $product->view = $view;
        $product->save();
      }
    }
  }
