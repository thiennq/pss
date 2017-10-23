<?php
use Slim\Container as ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use GuzzleHttp\Client;

class Order extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
    protected $table = 'order';

    public function store($customer_id, $data, $subTotal, $total, $id_odoo = -1) {
      $order = new Order;
      $order->customer_id = $customer_id;
      $order->payment_method = $data['payment_method'];
      $order->shipping_price = $data['shipping_price'];
      $order->discount = $data['discount'];
      $order->subtotal = $subTotal;
      $order->total = $total;
      $order->order_status = 'new';
      $order->payment_status = 0;
      $order->shipping_status = 0;
      $order->notes = $data['notes'] ? $data['notes'] : '';
      $order->reason_cancel = '';
      $order->created_at = date('Y-m-d H:i:s');
      $order->updated_at = date('Y-m-d H:i:s');
      if ($order->save()) return $order->id;
      return -3;
    }
}
