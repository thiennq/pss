<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Video extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'video';

  public function store($data) {
    $video = new Video;
    $video->title = $data->title;
    $video->embed_link = $data->embed_link;
    $video->created_at = date('Y-m-d H:i:s');
    $video->updated_at = date('Y-m-d H:i:s');
    $video->save();
    return $video->id;
  }
}
