<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Filter extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'filter';

  public function store($key, $value) {

  }

  public function list() {
    $data = Filter::orderBy('title', 'asc')->get();
    return $data;
  }

  public function getExceptPrice() {
    $data = Filter::where('type', '!=', 'price')->get();
    return $data;
  }
}
