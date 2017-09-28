<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class LogOrderOdoo extends Illuminate\Database\Eloquent\Model {
  public $timestamps = false;
  protected $table = 'log_order_odoo';

  public function store($order_id, $log) {
    $a = new LogOrderOdoo;
    $a->order_id = $order_id;
    $a->log = $log;
    $a->created_at = date('Y-m-d H:i:s');
    $a->updated_at = date('Y-m-d H:i:s');
    $a->save();
  }
}
