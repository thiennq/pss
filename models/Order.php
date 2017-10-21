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
      $order->id_odoo = $id_odoo;
      $order->customer_id = $customer_id;
      $order->payment_method = $data['payment_method'];
      $order->discount = $data['discount'];
      $order->shipping_price = $data['shipping_price'];
      $order->subtotal = $subTotal;
      $order->total = $total;
      $order->order_status = 'new';
      $order->created_at = date('Y-m-d H:i:s');
      $order->updated_at = date('Y-m-d H:i:s');
      if ($order->save()) return $order->id;
      return -3;
    }

    public function getInfoOdoo($order_id) {
      $order = Order::find($order_id);
      $cart = Cart::where('order_id', $order_id);
      $customer = Customer::find($order->customer_id);

      $order_odoo = new stdClass();
      $order_odoo->note = '';
      $order_odoo->city = $customer->region;
      $order_odoo->district = $customer->subregion;
      $order_odoo->address = $customer->address;

      $details = array();
      foreach ($cart as $key => $value) {
        $item = new stdClass();
        $item->id = $value->product_id;
        $item->unitprice = $value->price;
        $item->quantity = $value->quantity;
        $item->description = $value->product_id;
        array_push($details, $item);
      }
      $order_odoo->details = $details;

      $obj = new stdClass();
      $obj->name = $customer->name;
      $obj->gender = "male";
      $obj->email = $customer->email;
      $obj->phone = $customer->phone;
      $obj->address = $customer->address;
      $obj->region = $customer->region;
      $obj->subregion = $customer->subregion;
      $order_odoo->customer = $obj;
      return $order_odoo;
    }

    public function PHPMailer($to, $subject, $body, $text) {
  		$mail = new PHPMailer;
      include ROOT.'/framework/phpmailer.php';
  		$mail->isSMTP();
  		$mail->Host = $STMP_HOST;
  		$mail->SMTPAuth = true;
  		$mail->Username = $STMP_USERNAME;
  		$mail->Password = $STMP_PASSWORD;
  		$mail->SMTPSecure = $STMP_SECURE;
  		$mail->Port = $STMP_PORT;

  		$mail->setFrom($STMP_USERNAME, 'TV');
  		$mail->addAddress($to);

  		$mail->isHTML(true);

  		$mail->Subject = $subject;
  		$mail->Body    = $body;
  		$mail->AltBody = $text;
  		$mail->CharSet = "UTF-8";
  		$mail->FromName = "BALOHANGHIEU";

  		if($mail->send()) return true;
      return false;
  	}
}
