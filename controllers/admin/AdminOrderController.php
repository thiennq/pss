<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require_once("../models/Order.php");
require_once("../models/Customer.php");
require_once("../models/Cart.php");
require_once(ROOT . '/models/Product.php');


class AdminOrderController extends AdminController {

	public function index(Request $request, Response $response) {
		$params = $request->getQueryParams();
		$order_status = $params['order_status'];
		$payment_status = $params['payment_status'];
		$menu_child = 'all';

		if (isset($payment_status) && $payment_status == 1) $menu_child = 'paid';
		$query = Order::join('customer', 'customer.id', '=', 'order.customer_id');

		if (isset($order_status)) {
			$menu_child = $order_status;
			$query = $query->where('order.order_status', $order_status);
			if (isset($payment_status)) {
				$query = $query->where('order.payment_status', $payment_status);
				if ($payment_status == 0) $menu_child = 'unpaid';
				else $menu_child = 'paid';
			}
		}

		$data = $query->select('order.id', 'order.created_at', 'customer.name', 'order.total', 'order.order_status')->orderBy('id', 'desc')->get();

		return $this->view->render($response, 'admin/order', array(
			'$data' => $data,
			'menu_child' => $menu_child
		));
	}

	public function search(Request $request, Response $response) {
		return $this->view->render($response, 'admin/order_search');
	}

	public function show (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $order = Order::find($id);
		if(!$order) if (!$data) return $response->withRedirect('/admin/order');
    $order->discount = money($order->discount);
		$order->subtotal = money($order->subtotal);
		$order->total = money($order->total);
		$order->shipping_price = money($order->shipping_price);
		$cart = Cart::where('order_id', $id)->get();
		foreach ($cart as $key => $value) {
			$value->price = money($value->price);
			$order_id = $value['product_id'];
			$product = Product::find($order_id);
			$value->image = $product->featured_image;
			$value->title = $product->title;
			$value->handle = $product->handle;
		}
		$customer = Customer::find($order->customer_id);
		$subregion = SubRegion::find($customer->subregion);
		$customer->subregion = $subregion->name;
		$region = Region::find($customer->region);
		$customer->region = $region->name;
		return $this->view->render($response, 'admin/order_edit', array(
			'order' => $order,
			'cart' => $cart,
			'customer' => $customer
		));
	}

	public function update (Request $request, Response $response) {
    $id = $request->getAttribute('id');
		$body = $request->getParsedBody();
    $order = Order::find($id);
		if (!$order) {
			return $response->withJson([
				'code' => -1,
				'message' => 'not found'
			]);
		}
		$order->order_status = $body['order_status'];
		if ($order->save()) {
			return $response->withJson([
				'code' => 0,
				'message' => 'success'
			]);
		}
		return $response->withJson([
			'code' => -1,
			'message' => 'error'
		]);
	}
}
?>
