<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use forxer\Gravatar\Gravatar;

class ProductTag extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'product_tag';

  public function store($product_id, $tag_id) {
    $product_tag = new ProductTag();
    $product_tag->product_id = $product_id;
    $product_tag->tag_id = $tag_id;
    $product_tag->created_at = date('Y-m-d H:i:s');
    $product_tag->updated_at = date('Y-m-d H:i:s');
    if ($product_tag->save()) return $product_tag->id;
    return -3;
  }

  public function remove($id) {
    $product_tag = ProductTag::where('product_id', $id)->get();
    if(!$product_tag) return -2;
    if($product_tag->destroy()) return 0;
    return -3;
  }

  public function findViaProducId($id){
      $tag_id = ProductTag::where('product_id',$id)->select('tag_id')->get();
      if ($tag_id) return $tag_id;
  }
}
