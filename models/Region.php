<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Region extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'region';
  public function store($obj) {
    $check = Region::find($obj->id);
    if($check) return false;
    $region = new Region;
    $region->id = $obj->id;
    $region->name = $obj->name;
    $region->save();
  }
}
