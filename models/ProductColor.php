<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ProductColor extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'product_color';

  public function store ($product_id, $color_id) {
    $check = ProductColor::where('product_id', $product_id)->where('color_id', $color_id)->first();
    if(!$check) {
      $color = new ProductColor;
      $color->product_id = $product_id;
      $color->color_id = $color_id;
      $color->created_at = date('Y-m-d H:i:s');
      $color->updated_at = date('Y-m-d H:i:s');
      $color->save();
    }
  }
}
