<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ProductAttribute extends Illuminate\Database\Eloquent\Model {
    protected $table = 'product_attribute';

    public function getByProductId($productId) {
      $data = ProductAttribute::where('product_id', $productId)->get();
      return $data;
    }
    public function store($product_id, $attribute_id) {
        $temp = new ProductAttribute;
        $temp->product_id = $product_id;
        $temp->attribute_id = $attribute_id;
        $temp->created_at = date('Y-m-d H:i:s');
        $temp->updated_at = date('Y-m-d H:i:s');
        $temp->save();
    }
}
