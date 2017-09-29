<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Variant extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'variant';

  public function store($data) {
    $variant = Variant::where('product_id', $data['product_id'])->where('title', $data['title'])->get();
    if ($variant) return -1;
    $variant = new Variant;
    $variant->title = $data['title'];
    $variant->price = $data['price'];
    $variant->price_compare = $data['price_compare'] ? $data['price_compare'] : 0;
    $variant->quantity = $data->quantity ? $data->quantity : 1;
    $variant->save();
    return $variant->id;
  }
}
