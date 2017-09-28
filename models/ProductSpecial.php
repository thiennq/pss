<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ProductSpecial extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'product_special';

  public function store ($product_id, $special_id) {
    $check = ProductSpecial::where('product_id', $product_id)->where('special_id', $special_id)->first();
    if(!$check) {
      $color = new ProductSpecial;
      $color->product_id = $product_id;
      $color->special_id = $special_id;
      $color->created_at = date('Y-m-d H:i:s');
      $color->updated_at = date('Y-m-d H:i:s');
      $color->save();
    }
  }
}
