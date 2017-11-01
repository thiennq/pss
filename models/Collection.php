<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Collection extends Illuminate\Database\Eloquent\Model {

  public $timestamps = false;
  protected $table = 'collection';

  public function listAll() {
    $data = Collection::orderBy('title', 'asc')->get();
    return $data;
  }

  public function store($data) {
    $item = Collection::where('title', $data['title'])->first();
    if ($item) return -1;
    $item = new Collection;
    $item->parent_id = $data['parent_id'] ? $data['parent_id'] : -1;
    $item->title = $data['title'];
    $item->handle = createHandle($data['title']);
    $item->breadcrumb = $data['title'];
    $item->link = $item->handle;
    if ($data['parent_id'] != '-1') {
      $parent = Collection::find($data['parent_id']);
      $item->breadcrumb = $parent->breadcrumb . '/' . $data['title'];
      $item->link = $parent->link . '/' . $item->handle;
    }
    $item->description = $data['description'];
    $item->content = $data['content'];
    $item->image = $data['image'] ? renameOneImage($data['image'], 'collection_' . $item->handle) : '';
    $item->banner = $data['banner'] ? renameOneImage($data['banner'], 'collection_' . $item->handle . '_banner') : '';
    $item->display = $data['display'] ? 1 : 0;
    $item->created_at = date('Y-m-d H:i:s');
    $item->updated_at = date('Y-m-d H:i:s');
    if ($item->save()) return $item->id;
    return -3;
  }

  public function update($id, $data) {
    $item = Collection::find($id);
    if (!$item) return -2;
    $check = Collection::where('id', '!=', $id)->where('title', $data['title'])->first();
    if ($check) return -1;
    $item->parent_id = $data['parent_id'] ? $data['parent_id'] : -1;
    $item->title = $data['title'];
    $item->handle = createHandle($data['title']);
    $item->breadcrumb = $data['title'];
    $item->link = $item->handle;
    if ($data['parent_id'] != '-1') {
      $parent = Collection::find($data['parent_id']);
      $item->breadcrumb = $parent->breadcrumb . '/' . $data['title'];
      $item->link = $parent->link . '/' . $item->handle;
    }
    $item->description = $data['description'];
    $item->content = $data['content'];
    $item->image = $data['image'] ? renameOneImage($data['image'], 'collection_' . $item->handle) : '';
    $item->banner = $data['banner'] ? renameOneImage($data['banner'], 'collection_' . $item->handle . '_banner') : '';
    $item->display = $data['display'] ? 1 : 0;
    $item->updated_at = date('Y-m-d H:i:s');
    if ($item->save()) return 0;
    return -3;
  }

  public function remove($id) {
    $item = Collection::find($id);
    if (!$item) return -2;
    $image = $item->image;
    $banner = $item->banner;
    if ($item->delete()) {
      Collection::where('parent_id', $id)->update(['parent_id' => -1]);
      removeImage($image);
      removeImage($banner);
      return 0;
    }
    return -3;
  }
}
