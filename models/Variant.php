<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Variant extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'variant';

  public function store($data) {
    $variant = new Variant;
    $variant->product_id = $data['product_id'];
    $variant->title = $data['title'];
    $variant->price = $data['price'];
    $variant->price_compare = $data['price_compare'];
    $variant->inventory = $data['inventory'];
    $variant->created_at = date('Y-m-d H:i:s');
    $variant->updated_at = date('Y-m-d H:i:s');
    if ($variant->save()) return $variant->id;
    return -3;
  }

  public function update($id, $data) {
    $variant = Variant::find($id);
    if (!$variant) {
      return -2;
    }
    $variant->title = $data['title'];
    $variant->price = $data['price'];
    $variant->price_compare = $data['price_compare'];
    $variant->inventory = $data['inventory'];
    $variant->updated_at = date('Y-m-d H:i:s');
    if ($variant->save()) return 0;
    return -3;
  }
}
