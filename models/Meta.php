<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Meta extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'meta';

  public function store($key, $value) {
    $meta = Meta::where('key', $key)->first();
    if($meta) {
      $meta->value = $value;
      $meta->updated_at = date('Y-m-d H:i:s');
      $meta->save();
    } else {
      $meta = new Meta;
      $meta->key = $key;
      $meta->value = $value;
      $meta->created_at = date('Y-m-d H:i:s');
      $meta->updated_at = date('Y-m-d H:i:s');
      $meta->save();
    }
  }

}
