<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Filter extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'filter';

  public function fetch($page =  1, $perpage = 100) {
    $skip = ($page - 1) * $perpage;
    $data = Filter::where('parent_id', -1)->skip($skip)->take($perpage)->get();
    foreach ($data as $key => $value) {
      $id = $value->id;
      $child = Filter::where('parent_id', $id)->get();
      if (count($child)) $value->child = $child;
    }
    return $data;
  }

  public function get($id) {
    $data = Filter::find($id);
    if ($data) return $data;
    return -2;
  }

  public function store($data) {
    $filter = Filter::where('title', $data['title'])->first();
    if ($filter) return -1;
    $filter = new Filter;
    $filter->parent_id = $data['parent_id'] ? $data['parent_id'] : -1;
    $filter->title = $data['title'];
    $filter->value = $data['value'] ? $data['value'] : '';
    $filter->created_at = date('Y-m-d H:i:s');
    $filter->updated_at = date('Y-m-d H:i:s');
    if ($filter->save()) return $filter->id;
    return -3;
  }

  public function remove($id) {
    $filter = Filter::find($id);
    if (!$filter) return -2;
    if ($filter->delete()) {
      Filter::where('parent_id', $id)->delete();
      return 0;
    }
    return -3;
  }

}
