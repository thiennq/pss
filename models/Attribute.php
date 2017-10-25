<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Attribute extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'attribute';

  public function fetch($page =  1, $perpage = 100) {
    $skip = ($page - 1) * $perpage;
    $data = Attribute::where('parent_id', -1)->skip($skip)->take($perpage)->get();
    foreach ($data as $key => $value) {
      $id = $value->id;
      $child = Attribute::where('parent_id', $id)->where('parent_id', '!=', -1)->get();
      $value->child = 0;
      if (count($child)) $value->child = $child;
    }
    return $data;
  }

  public function get($id) {
    $data = Attribute::find($id);
    if ($data) return $data;
    return -2;
  }

  public function store($data) {
    if ($data['parent_id']) {
      $attr = Attribute::where('name', $data['name'])->where('parent_id', '!=', -1)->first();
    } else {
      $attr = Attribute::where('name', $data['name'])->where('parent_id', -1)->first();
    }
    if ($attr) return -1;
    $attr = new Attribute;
    $attr->parent_id = $data['parent_id'] ? $data['parent_id'] : -1;
    $attr->name = $data['name'];
    $attr->created_at = date('Y-m-d H:i:s');
    $attr->updated_at = date('Y-m-d H:i:s');
    if ($attr->save()) return $attr->id;
    return -3;
  }

  public function update($id, $data) {
    $attr = Attribute::where('name', $data['name'])->where('id', '!=', $id)->first();
    if ($attr) return -1;
    $attr = Attribute::find($id);
    if (!$attr) return -2;
    $attr->name = $data['name'];
    $attr->created_at = date('Y-m-d H:i:s');
    $attr->updated_at = date('Y-m-d H:i:s');
    if ($attr->save()) return 0;
    return -3;
  }

  public function remove($id) {
    $attr = Attribute::find($id);
    if (!$attr) return -2;
    if ($attr->delete()) {
      Attribute::where('parent_id', $id)->delete();
      return 0;
    }
    return -3;
  }

}
