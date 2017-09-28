<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Branch extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'branch';

  public function store($obj) {
    $check = Branch::find($obj->id);
    if($check) return false;
    $branch = new Branch;
    $branch->id = $obj->id;
    $branch->name = $obj->name;
    $branch->region_id = ($obj->region_id) ? $obj->region_id : '';
    $branch->address = ($obj->address) ? $obj->address : '';
    $branch->featured_image = ($obj->featured_image) ? $obj->featured_image : '';
    $branch->hotline = ($obj->hotline) ? $obj->hotline : '';
    $branch->open_hours = ($obj->open_hours) ? $obj->open_hours : '';
    $branch->close_hours = ($obj->close_hours) ? $obj->close_hours : '';
    $branch->link = ($obj->link) ? $obj->link : '';
    $branch->display = ($obj->display) ? $obj->display : '';
    $branch->created_at = date('Y-m-d H:i:s');
    $branch->updated_at = date('Y-m-d H:i:s');
    $branch->save();
    return $obj->id;
  }
}
