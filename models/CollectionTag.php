<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class CollectionTag extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'collection_tag';

    public function store($collection_id, $name, $handle) {
      $tag = CollectionTag::where('collection_id', $collection_id)->where('name', $name)->where('handle', $handle)->first();
      if(!$tag) {
        $tag = new CollectionTag;
        $tag->collection_id = $collection_id;
        $tag->name = $name;
        $tag->handle = $handle;
        $tag->created_at = date('Y-m-d H:i:s');
        $tag->updated_at = date('Y-m-d H:i:s');
        $tag->save();
      }
    }
}
