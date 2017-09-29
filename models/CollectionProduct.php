<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class CollectionProduct extends Illuminate\Database\Eloquent\Model {
    protected $table = 'collection_product';

    public function store($collection_id, $product_id) {
      $data = CollectionProduct::where('collection_id', $collection_id)->where('product_id', $product_id)->first();
      if(!$data) {
        $data = new CollectionProduct;
        $data->collection_id = $collection_id;
        $data->product_id = $product_id;
        $data->created_at = date('Y-m-d H:i:s');
        $data->updated_at = date('Y-m-d H:i:s');
        $data->save();
      }
    }
}
