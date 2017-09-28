<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class CollectionProduct extends Illuminate\Database\Eloquent\Model {
    protected $table = 'collection_product';

    public function store($collection_id, $product_id) {
      $temp = CollectionProduct::where('collection_id', $collection_id)->where('product_id', $product_id)->first();
      if(!$temp) {
        $temp = new CollectionProduct;
        $temp->collection_id = $collection_id;
        $temp->product_id = $product_id;
        $temp->created_at = date('Y-m-d H:i:s');
        $temp->updated_at = date('Y-m-d H:i:s');
        $temp->save();
      }
    }
}
