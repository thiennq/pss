<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Role extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'role';

  public function store($obj) {
    $role = Role::where('email', $obj->email)->first();
    if($role) {
      $role->product = $obj->product ? $obj->product : 0;
      $role->order = $obj->order ? $obj->order : 0;
      $role->customer = $obj->customer ? $obj->customer : 0;
      $role->article = $obj->article ? $obj->article : 0;
      $role->setting = $obj->setting ? $obj->setting : 0;
      $role->staff = $obj->staff ? $obj->staff : 0;
      $role->updated_at = date('Y-m-d H:i:s');
      $role->save();
    } else {
      $role = new Role;
      $role->email = $obj->email;
      $role->product = $obj->product ? $obj->product : 0;
      $role->order = $obj->order ? $obj->order : 0;
      $role->customer = $obj->customer ? $obj->customer : 0;
      $role->article = $obj->article ? $obj->article : 0;
      $role->setting = $obj->setting ? $obj->setting : 0;
      $role->staff = $obj->staff ? $obj->staff : 0;
      $role->created_at = date('Y-m-d H:i:s');
      $role->updated_at = date('Y-m-d H:i:s');
      $role->save();
    }
  }
}
