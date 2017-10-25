<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ProductFilter extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'product_filter';

  public function store($product_id, $filter_id) {
    $check = ProductFilter::where('product_id', $product_id)->where('filter_id', $filter_id)->first();
    if ($check) return -1;
		$product_filter = new ProductFilter;
		$product_filter->product_id = $product_id;
    $product_filter->filter_id = $filter_id;
    $product_filter->created_at = date('Y-m-d H:i:s');
    $product_filter->updated_at = date('Y-m-d H:i:s');
    $product_filter->save();
  }

  public function removeAll($article_id) {
    $result = ProductFilter::where('product_', $product_id)->delete();
    if ($result) return 0;
    return -3;
  }
}
