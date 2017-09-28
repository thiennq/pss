<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class CollectionLink extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'collection_link';

    public function store ($collection_id, $title, $url) {
      $check = CollectionLink::where('collection_id', $collection_id)->where('title', $title)->where('url', $url)->first();
      if(!$check) {
        $tag = new CollectionLink;
        $tag->collection_id = $collection_id;
        $tag->title = $title;
        $tag->url = $url;
        $tag->created_at = date('Y-m-d H:i:s');
        $tag->updated_at = date('Y-m-d H:i:s');
        $tag->save();
      }
    }
}
