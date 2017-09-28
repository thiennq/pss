<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Sort extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'sort';

  public function store($data) {
    $sort = new Sort;
    $sort->price = $data->price;
    $sort->created_at = date('Y-m-d H:i:s');
    $sort->updated_at = date('Y-m-d H:i:s');
    $sort->save();
    return $sort->id;
  }
}
