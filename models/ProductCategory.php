<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ProductCategory extends Illuminate\Database\Eloquent\Model {
    protected $table = 'category_product';

    public function getByProductId($productId) {
      $data = ProductCategory::where('product_id', $productId)->get();
      return $data;
    }
    public function store($category_id, $product_id) {
      $temp = ProductCategory::where('category_id', $category_id)->where('product_id', $product_id)->first();
      if(!$temp) {
        $temp = new ProductCategory;
        $temp->category_id = $category_id;
        $temp->product_id = $product_id;
        $temp->created_at = date('Y-m-d H:i:s');
        $temp->updated_at = date('Y-m-d H:i:s');
        $temp->save();
      }
    }
}
