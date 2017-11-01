<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class History extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'history';

  function store($content, $type, $type_id) {
    if (!$content || !$type || !$type_id) return -2;
    $item = new History;
    $item->user = $_SESSION['name'];
    $item->user_id = $_SESSION['user_id'];
    $item->content = $content;
    $item->type = $type;
    $item->type_id = $type_id;
    $item->created_at = date('Y-m-d H:i:s');
    $item->updated_at = date('Y-m-d H:i:s');
    $item->save();
    return 0;
  }
}
