<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Seo extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'seo';

  public function store($type, $type_id, $data) {
    $item = Seo::where('type', $type)->where('type_id', $type_id)->first();
    if (!$item) {
      $item = new Seo;
      $item->meta_title = $data['meta_title'];
      $item->meta_description = $data['meta_description'];
      $item->meta_keyword = $data['meta_keyword'];
      $item->meta_robots = $data['meta_robots'];
      $item->type = $type;
      $item->type_id = $type_id;
      $item->created_at = date('Y-m-d H:i:s');
      $item->updated_at = date('Y-m-d H:i:s');
      $item->save();
      return 0;
    }
    return -3;
  }

  public function update($type, $type_id, $data) {
    $item = Seo::where('type', $type)->where('type_id', $type_id)->first();
    if (!$item) return -2;
    $item->meta_title = $data['meta_title'];
    $item->meta_description = $data['meta_description'];
    $item->meta_keyword = $data['meta_keyword'];
    $item->meta_robots = $data['meta_robots'];
    $item->updated_at = date('Y-m-d H:i:s');
    $item->save();
    return 0;
  }

  public function get($type, $type_id) {
    $item = Seo::where('type', $type)->where('type_id', $type_id)->first();
    return $item;
  }

  public function remove($type, $type_id) {
    $item = Seo::where('type', $type)->where('type_id', $type_id)->first();
    if (!$item) return -2;
    $item->delete();
    return 0;
  }
}
