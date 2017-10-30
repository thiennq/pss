<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use forxer\Gravatar\Gravatar;

class Tag extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'tag';

  public function store($data) {
    $tag = new Tag;
    $tag->name = $data;
    $tag->created_at = date('Y-m-d H:i:s');
    $tag->updated_at = date('Y-m-d H:i:s');
    if ($tag->save()) return $tag->id;
  }

  public function update($id, $data) {
    $tag = Tag::find($id);
    if (!$tag) return -2;
    $tag->name = $data;
    $tag->updated_at = date('Y-m-d H:i:s');
    if ($tag->save()) return $tag->id;
    return -3;
  }

  public function remove($id) {
    $tag = Tag::find($id);
    if(!$tag) return -2;
    if($tag->delete()) return $tag->id;
    return -3;
  }
}
