<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class SubRegion extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'subregion';
  public function store($obj) {
    $check = SubRegion::find($obj->id);
    if($check) return false;
    $subregion = new SubRegion;
    $subregion->id = $obj->id;
    $subregion->name = $obj->name;
    $subregion->region_id = $obj->region_id;
    $subregion->save();
  }
}
