<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Cart extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'cart';

    public function store($data) {
      $product = Product::find($data->product_id);
      $cart = new Cart;
      $cart->order_id = $data->order_id;
      $cart->product_id = $data->product_id;
      $cart->price = $product->price;
      $cart->quantity = $data->quantity;
      $cart->created_at = date('Y-m-d H:i:s');
      $cart->updated_at = date('Y-m-d H:i:s');
      $cart->save();
    }
}
