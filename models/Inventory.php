<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Inventory extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'inventory';

  public function update($obj) {
    $inventory = Inventory::where('branch_id', $obj->branch_id)->where('product_id', $obj->product_id)->first();
    if($inventory) {
      if($inventory->inventory != $obj->inventory) {
        $inventory->inventory = $obj->inventory;
        $inventory->updated_at = date('Y-m-d H:i:s');
        $inventory->save();
      }
    } else {
      $inventory = new Inventory;
      $inventory->branch_id = $obj->branch_id;
      $inventory->product_id = $obj->product_id;
      $inventory->inventory = $obj->inventory ? $obj->inventory : 0;
      $inventory->created_at = date('Y-m-d H:i:s');
      $inventory->updated_at = date('Y-m-d H:i:s');
      $inventory->save();
    }
  }
}
