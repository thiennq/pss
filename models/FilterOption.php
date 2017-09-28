<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class FilterOption extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'filter_option';

  public function store($key, $value) {

  }
  public function list() {
    $data = FilterOption::orderBy('value', 'asc')->get();
    return $data;
  }
}
